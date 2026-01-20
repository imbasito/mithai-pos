@extends('backend.master')

@section('title', 'System & Updates')

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- License Section --}}
        <div class="col-md-6">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-key mr-2"></i>License Information
                    </h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <i class="fas fa-times-circle mr-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @if($licenseInfo && $licenseInfo['valid'])
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
                            </div>
                            <h4 class="text-success">License Active</h4>
                        </div>

                        <div class="card bg-light shadow-none border">
                            <div class="card-body p-0">
                                <table class="table table-sm mb-0">
                                    <tr>
                                        <td class="pl-3"><strong>Licensed To:</strong></td>
                                        <td>{{ $licenseInfo['shop'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="pl-3"><strong>Expires:</strong></td>
                                        <td>
                                            @if($licenseInfo['lifetime'])
                                                <span class="badge badge-success">Lifetime</span>
                                            @else
                                                {{ $licenseInfo['expiry'] }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="pl-3"><strong>Machine ID:</strong></td>
                                        <td><code>{{ $machineId }}</code></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-lock text-warning" style="font-size: 48px;"></i>
                            </div>
                            <h4 class="text-warning">License Required</h4>
                        </div>

                        <form action="{{ route('backend.admin.license.activate') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="license_key"><strong>License Key</strong></label>
                                <textarea class="form-control" id="license_key" name="license_key" rows="3" 
                                    placeholder="MPOS-xxxxxxxx-xxxxxxxx" required 
                                    style="font-family: monospace;"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-block mb-3">
                                <i class="fas fa-unlock mr-2"></i>Activate License
                            </button>
                        </form>

                        <div class="text-center pt-2 border-top">
                            <p class="text-muted small mb-1">Machine ID (provide this when requesting license):</p>
                            <code>{{ $machineId }}</code>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Update Section --}}
        <div class="col-md-6">
            <div class="card card-outline card-info" id="update-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-sync-alt mr-2"></i>Software Update
                    </h3>
                </div>
                <div class="card-body">
                    <div id="update-status-container" class="text-center py-3">
                        <div class="mb-3">
                            <i class="fas fa-cloud-download-alt text-info update-icon" style="font-size: 48px;"></i>
                        </div>
                        <h5 id="update-text">Check for the latest version</h5>
                        <p id="update-subtext" class="text-muted small">Stay up to date with new features and security fixes.</p>
                        
                        <div id="update-progress-container" class="d-none mt-3">
                            <div class="progress progress-sm mb-2">
                                <div id="update-progress-bar" class="progress-bar bg-info progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small id="update-progress-text" class="text-muted">Downloading: 0%</small>
                        </div>
                    </div>

                    <div id="update-actions" class="mt-4">
                        <button id="btn-check-update" class="btn btn-info btn-block border-0 px-4 py-2" style="border-radius: 8px; font-weight: 600;">
                            <i class="fas fa-search mr-2"></i>Check for Updates
                        </button>
                        
                        <div id="post-check-actions" class="d-none">
                            <button id="btn-download-update" class="btn btn-success btn-block border-0 px-4 py-2" style="border-radius: 8px; font-weight: 600;">
                                <i class="fas fa-download mr-2"></i>Download Update Now
                            </button>
                        </div>

                        <div id="ready-actions" class="d-none">
                            <button id="btn-install-update" class="btn btn-primary btn-block border-0 px-4 py-2" style="border-radius: 8px; font-weight: 600;">
                                <i class="fas fa-rocket mr-2"></i>Restart and Update
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 text-center">
                    <small class="text-muted">Current Version: {{ config('app.version', '1.0.0') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Contact Info Card --}}
<div class="row justify-content-center mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body py-2 text-center text-muted small">
                <strong>Need a license?</strong> <a href="tel:+923429031328" class="text-info mx-2"><i class="fas fa-phone-alt"></i> +92 342 9031328</a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnCheck = document.getElementById('btn-check-update');
        const btnDownload = document.getElementById('btn-download-update');
        const btnInstall = document.getElementById('btn-install-update');
        const updateText = document.getElementById('update-text');
        const updateSubtext = document.getElementById('update-subtext');
        const updateIcon = document.querySelector('.update-icon');
        const progressContainer = document.getElementById('update-progress-container');
        const progressBar = document.getElementById('update-progress-bar');
        const progressText = document.getElementById('update-progress-text');
        const postCheckActions = document.getElementById('post-check-actions');
        const readyActions = document.getElementById('ready-actions');

        if (typeof window.updater === 'undefined') {
            btnCheck.disabled = true;
            updateSubtext.innerText = "Updater only available in production desktop mode.";
            return;
        }

        btnCheck.addEventListener('click', () => {
            btnCheck.disabled = true;
            btnCheck.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Checking...';
            window.updater.check();
        });

        btnDownload.addEventListener('click', () => {
            postCheckActions.classList.add('d-none');
            progressContainer.classList.remove('d-none');
            updateText.innerText = 'Downloading New Version...';
            window.updater.download();
        });

        btnInstall.addEventListener('click', () => {
            window.updater.install();
        });

        // Listen for status updates
        window.updater.onStatus((status, info) => {
            btnCheck.disabled = false;
            btnCheck.innerHTML = '<i class="fas fa-search mr-2"></i>Check for Updates';

            if (status === 'available') {
                updateText.innerText = 'New Update Available!';
                updateSubtext.innerHTML = `Version <strong>${info.version}</strong> is ready for download.`;
                updateIcon.className = 'fas fa-arrow-alt-circle-up text-success update-icon';
                btnCheck.classList.add('d-none');
                postCheckActions.classList.remove('d-none');
            } else if (status === 'latest') {
                updateText.innerText = 'You are up to date!';
                updateSubtext.innerText = 'You are already running the latest version of SPOS.';
                updateIcon.className = 'fas fa-check-circle text-success update-icon';
            } else if (status === 'error') {
                updateText.innerText = 'Check Failed';
                updateSubtext.innerText = 'Unable to reach the update server. Please check your internet.';
                updateIcon.className = 'fas fa-exclamation-triangle text-danger update-icon';
            }
        });

        // Listen for progress updates
        window.updater.onProgress((progress) => {
            const percent = Math.floor(progress.percent);
            progressBar.style.width = percent + '%';
            progressText.innerText = `Downloading: ${percent}%`;
        });

        // Listen for ready state
        window.updater.onReady((info) => {
            progressContainer.classList.add('d-none');
            readyActions.classList.remove('d-none');
            updateText.innerText = 'Update Ready to Install!';
            updateSubtext.innerText = 'The download is complete. Please restart the app to apply changes.';
            updateIcon.className = 'fas fa-rocket text-primary update-icon blink';
        });
    });
</script>

<style>
    .blink {
        animation: blink-animation 1s steps(5, start) infinite;
    }
    @keyframes blink-animation {
        to { visibility: hidden; }
    }
</style>
@endpush
