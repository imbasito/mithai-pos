@extends('backend.master')

@section('title', 'License Activation')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-key mr-2"></i>License Activation
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
                        {{-- License Active --}}
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
                            </div>
                            <h4 class="text-success">License Active</h4>
                            <p class="text-muted">Your software is properly licensed</p>
                        </div>

                        <div class="card bg-light">
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <td><strong>Licensed To:</strong></td>
                                        <td>{{ $licenseInfo['shop'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Expires:</strong></td>
                                        <td>
                                            @if($licenseInfo['lifetime'])
                                                <span class="badge badge-success">Lifetime</span>
                                            @else
                                                {{ $licenseInfo['expiry'] }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Machine ID:</strong></td>
                                        <td><code>{{ $machineId }}</code></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                    @else
                        {{-- Not Licensed --}}
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-lock text-warning" style="font-size: 64px;"></i>
                            </div>
                            <h4 class="text-warning">License Required</h4>
                            <p class="text-muted">Please enter your license key to activate</p>
                        </div>

                        @if($licenseInfo && isset($licenseInfo['expired']))
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Your license for <strong>{{ $licenseInfo['shop'] }}</strong> has expired!
                                Please renew your license.
                            </div>
                        @endif

                        <form action="{{ route('backend.admin.license.activate') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="license_key"><strong>License Key</strong></label>
                                <textarea class="form-control" id="license_key" name="license_key" rows="3" 
                                    placeholder="MPOS-xxxxxxxx-xxxxxxxx" required 
                                    style="font-family: monospace;"></textarea>
                                <small class="text-muted">Paste your license key here</small>
                            </div>
                            <button type="submit" class="btn btn-success btn-block btn-lg">
                                <i class="fas fa-unlock mr-2"></i>Activate License
                            </button>
                        </form>

                        <hr>

                        <div class="text-center">
                            <p class="text-muted small mb-1">Machine ID (provide this when requesting license):</p>
                            <code>{{ $machineId }}</code>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Contact Info --}}
            <div class="card mt-3">
                <div class="card-body text-center">
                    <p class="mb-1"><strong>Need a license?</strong></p>
                    <p class="text-muted mb-0">Contact: <a href="tel:+923429031328">+92 342 9031328</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
