@extends('backend.layouts.app')

@section('title', 'Add FieldManager')

@push('styles')


@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">Add FieldManager</h4>
                <a href="{{ route('admin.field_manager.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>
            <!-- Card Body -->
            <div class="card-body mt-3">
                <form action="{{ route('admin.field_manager.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Name Field -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            value="{{ old('name') }}"
                            placeholder="Enter name"
                            required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            value="{{ old('email') }}"
                            placeholder="Enter email"
                            required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Mobile Field -->
                    <div class="mb-3">
                        <label for="mobile" class="form-label">Mobile</label>
                        <input
                            type="text"
                            name="mobile"
                            id="mobile"
                            class="form-control {{ $errors->has('mobile') ? 'is-invalid' : '' }}"
                            value="{{ old('mobile') }}"
                            placeholder="Enter mobile number"
                            required>
                        @error('mobile')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <!-- <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            placeholder="Enter password"
                            required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> -->

                    <!-- Image Upload -->
                    
                    <div class="mb-3">
                        <label for="slider-image-input" class="form-label">Profile Image</label>
                        <img src="{{ asset('assets/img/defaultprofile.png') }}" id="staff-imgsrc" class="img-thumbnail d-block mb-3" style="max-width: 150px; display: none;" alt="Staff Image">

                        <input
                            type="file"
                            name="image"
                            id="staff-image-input"
                            class="form-control {{ $errors->has('image') ? 'is-invalid' : '' }}"
                            accept="image/*">
                        @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-sm">
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
        $('#staff-image-btn').on('click', function() {
            $('#staff-image-input').click();
        });
        $('#staff-image-input').on('change', function() {
            showImage(this, '#staff-imgsrc');
        });
    })
</script>

@endpush