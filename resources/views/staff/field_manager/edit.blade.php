@extends('backend.layouts.app')

@section('title', 'Edit FieldManager')

@push('styles')


@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">EDIT FieldManager</h4>
                <a href="{{ route('staff.field_manager.index') }}" class="btn btn-default">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>
            <!-- Card Body -->
            <div class="card-body mt-3">
                <form action="{{ route('staff.field_manager.update', $field_manager->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            value="{{ old('name', $field_manager->name) }}"
                            placeholder="Enter name"
                            required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $field_manager->email) }}" placeholder="Enter email">
                        @error('email')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('mobile') ? 'focused error' : '' }}">
                            <label class="form-label">Mobile</label>
                            <input type="tel" placeholder="Enter Mobile" name="mobile" class="form-control" value="{{ old('mobile',$field_manager->mobile_no) }}" disabled>
                        </div>
                        @error('mobile')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Existing Image -->
                    @if(Storage::disk('public')->exists('field_manager/'.$field_manager->image))
                    <div class="mb-3">
                        <img
                            src="{{ Storage::url('field_manager/'.$field_manager->image) }}"
                            id="owner-imgsrc-edit"
                            alt="{{ $field_manager->name }}"
                            class="img-thumbnail"
                            style="max-width: 200px;">
                    </div>
                    @endif

                    <!-- Image Upload -->
                    <div class="mb-3">
                        <label for="owner-image-input-edit" class="form-label">Upload New Image</label>
                        <input
                            type="file"
                            name="image"
                            id="owner-image-input-edit"
                            class="form-control {{ $errors->has('image') ? 'is-invalid' : '' }}"
                            accept="image/*">
                        <img id="preview-image" src="#" alt="Preview" class="img-thumbnail mt-2 d-none" style="max-width: 200px;">
                        @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-sm mt-3">
                        Update
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection


@push('scripts')

<script>
    function showImage(fileInput, imgID) {
        if (fileInput.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $(imgID).attr('src', e.target.result);
                $(imgID).attr('alt', fileInput.files[0].name);
            }
            reader.readAsDataURL(fileInput.files[0]);
        }
    }
    $('#owner-image-btn-edit').on('click', function() {
        $('#owner-image-input-edit').click();
    });
    $('#owner-image-input-edit').on('change', function() {
        showImage(this, '#owner-imgsrc-edit');
    });
</script>

@endpush