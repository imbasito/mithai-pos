@extends('backend.master')

@section('title', 'Backup & Restore')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-database mr-2"></i>Backup & Restore
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row">
                        <!-- Backup Settings -->
                        <div class="col-md-4">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-cog mr-2"></i>Settings
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('backend.admin.settings.backup.save') }}" method="POST">
                                        @csrf
                                        <div class="form-group">
                                            <label for="backup_path">
                                                <i class="fas fa-folder mr-1"></i>Backup Directory
                                            </label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="backup_path" name="backup_path" 
                                                    value="{{ $backupPath }}" placeholder="C:\Users\...\Documents\Backups">
                                                <div class="input-group-append">
                                                    <button type="button" class="btn btn-secondary" id="browseBtn" title="Browse for folder">
                                                        <i class="fas fa-folder-open"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted">Where backup files will be saved</small>
                                            <!-- Hidden file input for folder selection -->
                                            <input type="file" id="folderPicker" webkitdirectory directory style="display: none;">
                                        </div>
                                        <div class="form-group">
                                            <label for="auto_backup">
                                                <i class="fas fa-clock mr-1"></i>Auto Backup
                                            </label>
                                            <select class="form-control" id="auto_backup" name="auto_backup">
                                                <option value="off" {{ $autoBackup == 'off' ? 'selected' : '' }}>Off</option>
                                                <option value="daily" {{ $autoBackup == 'daily' ? 'selected' : '' }}>Daily</option>
                                                <option value="weekly" {{ $autoBackup == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-0">
                                            <p class="mb-1"><strong>Last Backup:</strong></p>
                                            <p class="text-muted mb-0">{{ $lastBackup }}</p>
                                        </div>
                                        <hr>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fas fa-save mr-1"></i>Save Settings
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="card card-outline card-success mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-bolt mr-2"></i>Quick Actions
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('backend.admin.settings.backup.create') }}" method="POST" id="backupForm">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-block btn-lg" id="createBackupBtn">
                                            <i class="fas fa-download mr-2"></i>Create Backup Now
                                        </button>
                                    </form>
                                    <p class="text-muted text-center mt-2 mb-0">
                                        <small>Creates a complete database backup</small>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Backup List -->
                        <div class="col-md-8">
                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-list mr-2"></i>Available Backups
                                    </h5>
                                </div>
                                <div class="card-body p-0">
                                    @if(count($backups) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Filename</th>
                                                    <th>Size</th>
                                                    <th>Date</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($backups as $backup)
                                                <tr>
                                                    <td>
                                                        <i class="fas fa-file-archive text-muted mr-2"></i>
                                                        {{ $backup['name'] }}
                                                    </td>
                                                    <td>{{ $backup['size'] }}</td>
                                                    <td>{{ $backup['date'] }}</td>
                                                    <td class="text-center">
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="{{ route('backend.admin.settings.backup.download', $backup['name']) }}" 
                                                               class="btn btn-info" title="Download">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-warning restore-btn" 
                                                                    data-file="{{ $backup['name'] }}" title="Restore">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-danger delete-btn" 
                                                                    data-file="{{ $backup['name'] }}" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No backups found</p>
                                        <p class="text-muted small">Click "Create Backup Now" to create your first backup</p>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Warning -->
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>Warning:</strong> Restoring a backup will replace all current data. Make sure to create a backup of current data before restoring.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2"></i>Confirm Restore</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to restore this backup?</p>
                <p class="font-weight-bold" id="restoreFileName"></p>
                <p class="text-danger"><strong>Warning:</strong> This will replace ALL current data!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form action="{{ route('backend.admin.settings.backup.restore') }}" method="POST" id="restoreForm">
                    @csrf
                    <input type="hidden" name="backup_file" id="restoreFileInput">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-undo mr-1"></i>Yes, Restore
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash mr-2"></i>Confirm Delete</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this backup?</p>
                <p class="font-weight-bold" id="deleteFileName"></p>
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form action="{{ route('backend.admin.settings.backup.delete') }}" method="POST" id="deleteForm">
                    @csrf
                    <input type="hidden" name="backup_file" id="deleteFileInput">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-1"></i>Yes, Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$(document).ready(function() {
    // Browse button - open folder picker
    $('#browseBtn').on('click', function() {
        $('#folderPicker').click();
    });
    
    // Handle folder selection
    $('#folderPicker').on('change', function(e) {
        if (this.files && this.files.length > 0) {
            // Get the folder path from the first file's webkitRelativePath
            var fullPath = this.files[0].webkitRelativePath;
            var folderPath = fullPath.split('/')[0];
            
            // For Electron apps, we can get the actual path
            if (this.files[0].path) {
                var filePath = this.files[0].path;
                folderPath = filePath.substring(0, filePath.lastIndexOf('\\'));
            }
            
            $('#backup_path').val(folderPath);
        }
    });

    // Create Backup - show loading
    $('#backupForm').on('submit', function() {
        $('#createBackupBtn').html('<i class="fas fa-spinner fa-spin mr-2"></i>Creating Backup...').prop('disabled', true);
    });

    // Restore button click
    $('.restore-btn').on('click', function() {
        var filename = $(this).data('file');
        $('#restoreFileName').text(filename);
        $('#restoreFileInput').val(filename);
        $('#restoreModal').modal('show');
    });

    // Delete button click
    $('.delete-btn').on('click', function() {
        var filename = $(this).data('file');
        $('#deleteFileName').text(filename);
        $('#deleteFileInput').val(filename);
        $('#deleteModal').modal('show');
    });

    // Restore form submit - show loading
    $('#restoreForm').on('submit', function() {
        $(this).find('button[type=submit]').html('<i class="fas fa-spinner fa-spin mr-1"></i>Restoring...').prop('disabled', true);
    });
});
</script>
@endpush
