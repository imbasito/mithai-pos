@extends('backend.master')

@section('title', 'System & Updates')

@section('content')
<div class="row animate__animated animate__fadeIn">
    {{-- License Section --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 border-radius-15 h-100">
            <div class="card-header bg-gradient-maroon py-3">
                <h5 class="card-title text-white font-weight-bold mb-0">
                    <i class="fas fa-key mr-2"></i> License Information
                </h5>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <i class="fas fa-times-circle mr-2"></i>{{ session('error') }}
                    </div>
                @endif

                @if($licenseInfo && $licenseInfo['valid'])
                    <div class="text-center mb-5 mt-3">
                        <div class="mb-3">
                            <i class="fas fa-certificate text-success" style="font-size: 64px;"></i>
                        </div>
                        <h3 class="text-success font-weight-bold">License Active</h3>
                        <p class="text-muted">Your copy of SPOS is fully activated.</p>
                    </div>

                    <div class="card bg-light shadow-none border border-radius-10">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <span class="text-muted">Licensed To:</span>
                                <span class="font-weight-bold ml-auto">{{ $licenseInfo['shop'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <span class="text-muted">Expires:</span>
                                @if($licenseInfo['lifetime'])
                                    <span class="badge badge-success px-3 py-1">Lifetime</span>
                                @else
                                    <span class="font-weight-bold text-danger">{{ $licenseInfo['expiry'] }}</span>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Machine ID:</span>
                                <code class="bg-white px-2 py-1 rounded border">{{ $machineId }}</code>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center mb-4 mt-3">
                        <div class="mb-3">
                            <i class="fas fa-lock text-warning" style="font-size: 64px;"></i>
                        </div>
                        <h3 class="text-warning font-weight-bold">License Required</h3>
                        <p class="text-muted">Please activate your software to continue.</p>
                    </div>

                    <form action="{{ route('backend.admin.license.activate') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="license_key" class="font-weight-bold">License Key</label>
                            <textarea class="form-control border-radius-10" id="license_key" name="license_key" rows="3" 
                                placeholder="Paste your license key here..." required 
                                style="font-family: monospace; background-color: #f8f9fa;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-maroon btn-block py-2 font-weight-bold shadow-sm border-radius-25">
                            <i class="fas fa-unlock mr-2"></i> Activate License
                        </button>
                    </form>

                    <div class="text-center pt-3 mt-3 border-top">
                        <p class="text-muted small mb-1">Share this Machine ID for activation:</p>
                        <code class="bg-light px-3 py-1 rounded border d-inline-block">{{ $machineId }}</code>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Update Section --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 border-radius-15 h-100" id="update-card">
            <div class="card-header bg-dark py-3">
                <h5 class="card-title text-white font-weight-bold mb-0">
                    <i class="fas fa-sync-alt mr-2"></i> Software Update
                </h5>
            </div>
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                <div id="update-status-container" class="py-3">
                    <div class="mb-4">
                        <div class="icon-circle bg-light text-info shadow-sm mx-auto" style="width: 100px; height: 100px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-cloud-download-alt fa-3x update-icon"></i>
                        </div>
                    </div>
                    <h4 id="update-text" class="font-weight-bold mb-2">Check for Updates</h4>
                    <p id="update-subtext" class="text-muted small mb-4">Current Version: <span class="badge badge-secondary ml-1">{{ config('app.version', '1.0.0') }}</span></p>
                    
                    <div id="update-progress-container" class="d-none mt-4 text-left">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="font-weight-bold small">Downloading...</span>
                            <small id="update-progress-text" class="font-weight-bold">0%</small>
                        </div>
                        <div class="progress progress-sm" style="height: 10px; border-radius: 5px;">
                            <div id="update-progress-bar" class="progress-bar bg-maroon progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <div id="update-actions" class="mt-auto w-100">
                    <button id="btn-check-update" class="btn btn-dark btn-block py-3 font-weight-bold border-radius-10 shadow-sm transition-hover">
                        <i class="fas fa-search mr-2"></i> Check Now
                    </button>
                    
                    <div id="post-check-actions" class="d-none">
                        <button id="btn-download-update" class="btn btn-success btn-block py-3 font-weight-bold border-radius-10 shadow-sm transition-hover">
                            <i class="fas fa-download mr-2"></i> Download Update
                        </button>
                    </div>

                    <div id="ready-actions" class="d-none">
                        <button id="btn-install-update" class="btn btn-primary btn-block py-3 font-weight-bold border-radius-10 shadow-sm transition-hover">
                            <i class="fas fa-rocket mr-2"></i> Restart & Install
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

{{-- Contact Info Card --}}
<div class="row justify-content-center mt-4">
    <div class="col-md-12">
        <div class="card shadow-sm border-0 border-radius-10 bg-white">
            <div class="card-body py-2 text-center text-muted small">
                <strong>Need help?</strong> <a href="tel:+923429031328" class="text-maroon font-weight-bold mx-2 hover-underline"><i class="fas fa-phone-alt"></i> +92 342 9031328</a>
                <span class="mx-2">|</span>
                <a href="#" class="text-maroon font-weight-bold hover-underline">Support Center</a>
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
    .border-radius-10 { border-radius: 10px; }
    .border-radius-15 { border-radius: 15px; }
    .border-radius-25 { border-radius: 25px; }
    .transition-hover:hover { transform: translateY(-3px); }
    .hover-underline:hover { text-decoration: underline; }
    .blink { animation: blink-animation 1s steps(5, start) infinite; }
    @keyframes blink-animation { to { visibility: hidden; } }
</style>
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
            btnCheck.classList.add('opacity-50');
            updateSubtext.innerText = "Updater available in Desktop App only.";
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
            updateText.innerText = 'Downloading Update...';
            window.updater.download();
        });

        btnInstall.addEventListener('click', () => {
            window.updater.install();
        });

        window.updater.onStatus((status, info) => {
            btnCheck.disabled = false;
            btnCheck.innerHTML = '<i class="fas fa-search mr-2"></i>Check Again';

            if (status === 'available') {
                updateText.innerText = 'New Version Available!';
                updateSubtext.innerHTML = `Version <strong class="text-success">${info.version}</strong> ready to download.`;
                updateIcon.className = 'fas fa-arrow-alt-circle-up text-success update-icon fa-3x';
                btnCheck.classList.add('d-none');
                postCheckActions.classList.remove('d-none');
            } else if (status === 'latest') {
                updateText.innerText = 'You are up to date!';
                updateSubtext.innerText = 'Running the latest version.';
                updateIcon.className = 'fas fa-check-circle text-success update-icon fa-3x';
            } else if (status === 'error') {
                updateText.innerText = 'Connection Failed';
                updateSubtext.innerText = 'Could not reach update server.';
                updateIcon.className = 'fas fa-exclamation-triangle text-danger update-icon fa-3x';
            }
        });

        window.updater.onProgress((progress) => {
            const percent = Math.floor(progress.percent);
            progressBar.style.width = percent + '%';
            progressText.innerText = `${percent}%`;
        });

        window.updater.onReady((info) => {
            progressContainer.classList.add('d-none');
            readyActions.classList.remove('d-none');
            updateText.innerText = 'Ready to Install!';
            updateSubtext.innerText = 'Download complete. Restart to apply.';
            updateIcon.className = 'fas fa-rocket text-primary update-icon fa-3x blink';
        });
    });
</script>
@endpush
