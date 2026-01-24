<form action="{{ route('backend.admin.settings.website.info.update') }}" method="post">
    @csrf
    <div class="card shadow-sm border-0 border-radius-15 mb-4">
        <div class="card-header bg-gradient-maroon py-3 d-flex justify-content-between align-items-center">
            <h5 class="text-white mb-0 font-weight-bold">
                <i class="fas fa-desktop mr-2"></i> Website Info
            </h5>
            <button type="submit" class="btn btn-light text-maroon font-weight-bold shadow-sm ml-auto">
                <i class="fas fa-save mr-1"></i> Save Changes
            </button>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Website Title <span class="text-danger">*</span></label>
                        <input class="form-control border-radius-10" name="site_name" type="text"
                            value="{{ readConfig('site_name') }}" placeholder="Enter Site Title">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Website URL</label>
                        <input class="form-control border-radius-10" name="site_url" type="text"
                            value="{{ readConfig('site_url') }}" placeholder="Enter Site URL (Optional)">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Meta Description</label>
                        <textarea class="form-control border-radius-10" rows="3" name="meta_description"
                            placeholder="Enter Meta Description">{{ readConfig('meta_description') }}</textarea>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Meta Keywords</label>
                        <textarea class="form-control border-radius-10" rows="3" name="meta_keywords"
                            placeholder="Enter Keywords (comma separated)">{{ readConfig('meta_keywords') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
