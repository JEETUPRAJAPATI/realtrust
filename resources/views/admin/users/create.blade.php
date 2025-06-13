@extends('backend.layouts.app')

@section('title', 'Create Sliders')

@push('styles')


@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">CREATE USERS</h4>
                <a href="{{ route('admin.user.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>

            <!-- Card Body -->
            <div class="card-body mt-3">
                <form action="{{ route('admin.user.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Name Input -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter name">
                        @error('name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Input -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Enter email">
                        @error('email')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Mobile Input -->
                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('mobile') ? 'focused error' : '' }}">
                            <label class="form-label">Mobile</label>
                            <input type="tel" placeholder="Enter Mobile" name="mobile" class="form-control" value="{{ old('mobile') }}" required>
                        </div>
                        @error('mobile')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-3">
                        <label for="slider-image-input" class="form-label">Upload Image</label>
                        <input type="file" id="slider-image-input" name="image" class="form-control">
                        @error('image')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="mt-2">
                            <img src="" id="slider-imgsrc" class="img-fluid rounded" style="max-width: 200px; display: none;">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">
                        Save
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

@endsection


@push('scripts')

<script>
    $(function() {
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
        $('#slider-image-btn').on('click', function() {
            $('#slider-image-input').click();
        });
        $('#slider-image-input').on('change', function() {
            showImage(this, '#slider-imgsrc');
        });
    })
</script>

@endpush