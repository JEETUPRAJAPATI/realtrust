@extends('backend.layouts.app')

@section('title', 'Add Staff')

@push('styles')


@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0"> CREATE STAFF</h4>
                <a href="{{ route('admin.staff.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>
            <!-- Card Body -->
            <div class="card-body mt-3">
                <form action="{{ route('admin.staff.store') }}" method="POST" enctype="multipart/form-data" class="p-4">
                    @csrf

                    <!-- Name Input -->
                    <div class="mb-3">
                        <label for="staff-name" class="form-label">Name</label>
                        <input type="text" name="name" id="staff-name" class="form-control" placeholder="Enter staff name" required>
                    </div>

                    <!-- Email Input -->
                    <div class="mb-3">
                        <label for="staff-email" class="form-label">Email</label>
                        <input type="email" name="email" id="staff-email" class="form-control" placeholder="Enter staff email" required>
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
                    <!-- Password Input -->
                    <!-- <div class="mb-3">
                        <label for="staff-password" class="form-label">Password</label>
                        <input type="password" name="password" id="staff-password" class="form-control" placeholder="Enter password" required>
                    </div> -->

                    <!-- Image Upload -->
                    <div class="mb-3">
                        <label for="staff-image-input" class="form-label">Profile Image</label>
                        <img src="{{ asset('assets/img/defaultprofile.png') }}" id="staff-imgsrc" class="img-thumbnail d-block mb-3" style="max-width: 150px; display: none;" alt="Staff Image">
                        <input type="file" name="image" id="staff-image-input" class="form-control" accept="image/*">
                        @error('image')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-sm">
                        <span>Save</span>
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