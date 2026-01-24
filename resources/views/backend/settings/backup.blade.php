@extends('backend.master')

@section('title', 'Backup Manager')

@section('content')
<div class="row animate__animated animate__fadeIn">
    <div class="col-md-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- Create & Settings Section -->
    <div class="col-md-5">
        <div class="row">
            {{-- Manual Backup Card --}}
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0 border-radius-15 h-100">
                    <div class="card-header bg-gradient-maroon py-3">
                        <h5 class="card-title text-white font-weight-bold mb-0">
                            <i class="fas fa-magic mr-2"></i> Actions
                        </h5>
                    </div>
                    <div class="card-body p-4 text-center">
                         <div class="d-flex align-items-center justify-content-center mb-4">
                             <div class="icon-circle bg-light text-maroon shadow-sm mr-3" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-hdd fa-2x"></i>
                            </div>
                            <div class="text-left">
                                <h5 class="font-weight-bold mb-1">Backup Database</h5>
                                <p class="text-muted mb-0 small">Create a safe snapshot now.</p>
                            </div>
                        </div>
                        
                        <form action="{{ route('backend.admin.settings.backup.create') }}" method="post" class="w-100">
                            @csrf
                            <button type="submit" class="btn btn-maroon btn-block py-2 shadow-sm font-weight-bold border-radius-10">
                                <i class="fas fa-plus-circle mr-2"></i> Create New Backup
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Settings Card --}}
            <div class="col-12">
                 <div class="card shadow-sm border-0 border-radius-15 h-100">
                    <div class="card-header bg-dark py-3">
                        <h5 class="card-title text-white font-weight-bold mb-0">
                            <i class="fas fa-cogs mr-2"></i> Configuration
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('backend.admin.settings.backup.save') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label class="font-weight-bold">Backup Directory Path</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-0"><i class="fas fa-folder text-secondary"></i></span>
                                    </div>
                                    <input type="text" name="backup_path" id="backup_path_input" class="form-control" value="{{ $backupPath }}" placeholder="e.g. D:/Backups/SPOS">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-secondary border-0" id="btn-browse-dir" title="Browse Directory">
                                            <i class="fas fa-folder-open"></i> Browse
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted">Absolute path to store files.</small>
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', () => {
                                    const btnBrowse = document.getElementById('btn-browse-dir');
                                    const inputPath = document.getElementById('backup_path_input');
                                    
                                    if (window.electron && window.electron.openDirectory) {
                                        btnBrowse.addEventListener('click', async () => {
                                            try {
                                                const path = await window.electron.openDirectory();
                                                if (path) {
                                                    inputPath.value = path;
                                                }
                                            } catch (e) {
                                                console.error('Directory select failed', e);
                                            }
                                        });
                                    } else {
                                        btnBrowse.style.display = 'none'; // Hide if not in Electron
                                    }
                                });
                            </script>

                            <div class="form-group">
                                <label class="font-weight-bold">Auto Backup Frequency</label>
                                <select name="auto_backup" class="form-control custom-select">
                                    <option value="off" {{ $autoBackup == 'off' ? 'selected' : '' }}>Disabled</option>
                                    <option value="daily" {{ $autoBackup == 'daily' ? 'selected' : '' }}>Daily (Once a day)</option>
                                    <option value="weekly" {{ $autoBackup == 'weekly' ? 'selected' : '' }}>Weekly (Every Sunday)</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-secondary btn-block shadow-sm font-weight-bold">
                                <i class="fas fa-save mr-2"></i> Save Settings
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup List Section -->
    <div class="col-md-7">
        <div class="card shadow-sm border-0 border-radius-15 h-100">
             <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                <h5 class="card-title text-dark font-weight-bold mb-0">
                    <i class="fas fa-history mr-2 text-secondary"></i> Backup History
                </h5>
                <span class="badge badge-light border ml-auto">{{ count($backups) }} Files</span>
            </div>
            <div class="card-body p-0 table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light text-uppercase small text-muted">
                        <tr>
                            <th class="pl-4 border-0">Filename</th>
                            <th class="border-0">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($backups as $backup)
                        <tr>
                            <td class="pl-4 align-middle">
                                <div class="font-weight-bold text-dark">{{ $backup['filename'] }}</div>
                                <div class="small text-muted">
                                    <i class="far fa-clock mr-1"></i> {{ $backup['date'] }} 
                                    <span class="mx-1">•</span> 
                                    <i class="fas fa-weight-hanging mr-1"></i> {{ $backup['size'] }}
                                </div>
                            </td>
                            <td class="text-right pr-4 align-middle">
                                <div class="btn-group">
                                    <a href="{{ route('backend.admin.settings.backup.restore', $backup['filename']) }}" onclick="return confirm('WARNING: This will overwrite your current database. Are you sure?')" class="btn btn-sm btn-warning shadow-sm font-weight-bold text-dark px-3" title="Restore Database">
                                        <i class="fas fa-undo mr-1"></i> Restore
                                    </a>
                                    <a href="{{ route('backend.admin.settings.backup.download', $backup['filename']) }}" class="btn btn-sm btn-light border shadow-sm" title="Download">
                                        <i class="fas fa-download text-primary"></i>
                                    </a>
                                     <a href="{{ route('backend.admin.settings.backup.delete', $backup['filename']) }}" onclick="return confirm('Delete this backup file?')" class="btn btn-sm btn-light border shadow-sm" title="Delete">
                                        <i class="fas fa-trash text-danger"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 text-light-gray"></i>
                                <p class="mb-0">No backup files found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-maroon {
        background: linear-gradient(45deg, #800000, #A01010) !important;
    }
    .text-maroon {
        color: #800000 !important;
    }
    .btn-maroon {
        background-color: #800000;
        color: white;
        transition: all 0.3s;
    }
    .btn-maroon:hover {
        background-color: #600000;
        color: white;
        transform: translateY(-2px);
    }
    .border-radius-15 { border-radius: 15px; }
    .border-radius-25 { border-radius: 25px; }
    .text-light-gray { color: #e9ecef; }
</style>
@endsection
