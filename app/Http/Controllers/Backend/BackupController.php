<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupController extends Controller
{
    protected $defaultBackupPath;
    protected $mysqlPath;
    protected $mysqldumpPath;

    public function __construct()
    {
        // Default backup directory in user's Documents
        $this->defaultBackupPath = getenv('USERPROFILE') . '\\Documents\\SPOS Backups';
        
        // MySQL tools path (relative to app root)
        $basePath = base_path();
        $this->mysqlPath = $basePath . '\\mysql\\bin\\mysql.exe';
        $this->mysqldumpPath = $basePath . '\\mysql\\bin\\mysqldump.exe';
    }

    /**
     * Get backup settings page
     */
    public function index()
    {
        $backupPath = readConfig('backup_path') ?: $this->defaultBackupPath;
        $autoBackup = readConfig('auto_backup') ?: 'off';
        $lastBackup = readConfig('last_backup_date') ?: 'Never';
        
        // Get list of existing backups
        $backups = $this->getBackupsList($backupPath);
        
        return view('backend.settings.backup', compact('backupPath', 'autoBackup', 'lastBackup', 'backups'));
    }

    /**
     * Save backup settings
     */
    public function saveSettings(Request $request)
    {
        $request->validate([
            'backup_path' => 'required|string',
            'auto_backup' => 'required|in:off,daily,weekly',
        ]);

        // Update config
        writeConfig('backup_path', $request->backup_path);
        writeConfig('auto_backup', $request->auto_backup);

        // Create directory if not exists
        if (!File::exists($request->backup_path)) {
            File::makeDirectory($request->backup_path, 0755, true);
        }

        return redirect()->back()->with('success', 'Backup settings saved successfully!');
    }

    /**
     * Create a new backup
     */
    public function createBackup(Request $request)
    {
        try {
            $backupPath = readConfig('backup_path') ?: $this->defaultBackupPath;
            
            // Create directory if not exists
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            // Generate filename with timestamp
            $filename = 'pos_db_' . date('Y-m-d_H-i-s') . '.sql';
            $fullPath = $backupPath . '\\' . $filename;

            // Get database credentials
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $port = config('database.connections.mysql.port');

            // Build mysqldump command using shell - bypass TCP socket issues
            $passwordPart = !empty($password) ? "-p{$password}" : '';
            $command = "\"{$this->mysqldumpPath}\" -u{$username} {$passwordPart} -P{$port} --protocol=pipe --single-transaction --routines --triggers {$database} > \"{$fullPath}\" 2>&1";
            
            // Execute command
            $output = shell_exec($command);
            
            // Check if file was created and has content
            if (!File::exists($fullPath)) {
                throw new \Exception('Backup file was not created. Output: ' . $output);
            }
            
            $fileSize = File::size($fullPath);
            if ($fileSize < 100) {
                // File too small, might be an error
                $content = File::get($fullPath);
                if (strpos($content, 'error') !== false || strpos($content, 'Error') !== false) {
                    File::delete($fullPath);
                    throw new \Exception('Backup failed: ' . $content);
                }
            }

            // Update last backup date
            writeConfig('last_backup_date', date('Y-m-d H:i:s'));

            // Get file size formatted
            $fileSizeFormatted = $this->formatBytes($fileSize);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Backup created successfully! ({$fileSizeFormatted})",
                    'filename' => $filename,
                    'size' => $fileSizeFormatted,
                    'date' => date('Y-m-d H:i:s')
                ]);
            }

            return redirect()->back()->with('success', "Backup created: {$filename} ({$fileSizeFormatted})");

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Backup failed: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Restore from a backup file
     */
    public function restoreBackup(Request $request)
    {
        try {
            $request->validate([
                'backup_file' => 'required|string'
            ]);

            $backupPath = readConfig('backup_path') ?: $this->defaultBackupPath;
            $fullPath = $backupPath . '\\' . $request->backup_file;

            if (!File::exists($fullPath)) {
                throw new \Exception('Backup file not found: ' . $request->backup_file);
            }

            // Get database credentials
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');

            // Build mysql command
            $command = [
                $this->mysqlPath,
                '--user=' . $username,
                '--host=' . $host,
                '--port=' . $port,
                $database,
                '-e',
                'source ' . str_replace('\\', '/', $fullPath)
            ];

            if (!empty($password)) {
                array_splice($command, 1, 0, '--password=' . $password);
            }

            $process = new Process($command);
            $process->setTimeout(600); // 10 minutes timeout
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Database restored successfully from: ' . $request->backup_file
                ]);
            }

            return redirect()->back()->with('success', 'Database restored successfully!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restore failed: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete a backup file
     */
    public function deleteBackup(Request $request)
    {
        try {
            $request->validate([
                'backup_file' => 'required|string'
            ]);

            $backupPath = readConfig('backup_path') ?: $this->defaultBackupPath;
            $fullPath = $backupPath . '\\' . $request->backup_file;

            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Backup deleted successfully!'
                ]);
            }

            return redirect()->back()->with('success', 'Backup deleted successfully!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Delete failed: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file
     */
    public function downloadBackup($filename)
    {
        $backupPath = readConfig('backup_path') ?: $this->defaultBackupPath;
        $fullPath = $backupPath . '\\' . $filename;

        if (!File::exists($fullPath)) {
            return redirect()->back()->with('error', 'Backup file not found!');
        }

        return response()->download($fullPath, $filename);
    }

    /**
     * Get list of backup files
     */
    protected function getBackupsList($path)
    {
        $backups = [];

        if (File::exists($path)) {
            $files = File::files($path);
            foreach ($files as $file) {
                if ($file->getExtension() === 'sql') {
                    $backups[] = [
                        'name' => $file->getFilename(),
                        'size' => $this->formatBytes($file->getSize()),
                        'date' => date('Y-m-d H:i:s', $file->getMTime())
                    ];
                }
            }
            // Sort by date descending
            usort($backups, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }

        return $backups;
    }

    /**
     * Format bytes to human readable
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Run auto backup (called by scheduler)
     */
    public function runAutoBackup()
    {
        $autoBackup = readConfig('auto_backup') ?: 'off';
        
        if ($autoBackup === 'off') {
            return;
        }

        $lastBackup = readConfig('last_backup_date');
        $now = now();
        
        $shouldBackup = false;
        
        if (!$lastBackup) {
            $shouldBackup = true;
        } else {
            $lastBackupDate = \Carbon\Carbon::parse($lastBackup);
            
            if ($autoBackup === 'daily' && $lastBackupDate->diffInDays($now) >= 1) {
                $shouldBackup = true;
            } else if ($autoBackup === 'weekly' && $lastBackupDate->diffInDays($now) >= 7) {
                $shouldBackup = true;
            }
        }

        if ($shouldBackup) {
            $this->createBackup(new Request());
        }
    }
}
