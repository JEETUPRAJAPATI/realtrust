@extends('backend.layouts.app')

@section('title', 'Add Owner')

@push('styles')


@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">Add Owner</h4>
                <a href="{{ route('admin.owner.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>

            <!-- Card Body -->
            <div class="card-body mt-3">
                <form action="{{ route('admin.owner.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Name Input -->
                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('name') ? 'focused error' : '' }}">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" placeholder="Enter Name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email Input -->
                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('email') ? 'focused error' : '' }}">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" placeholder="Enter email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        @error('email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Mobile Input -->
                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('mobile') ? 'focused error' : '' }}">
                            <label class="form-label">Mobile</label>
                            <input type="tel" name="mobile" placeholder="Enter Mobile" class="form-control" value="{{ old('mobile') }}" required>
                        </div>
                        @error('mobile')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password Input -->
                    <!-- <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('password') ? 'focused error' : '' }}">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        @error('password')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div> -->

                    <!-- Image Upload -->
                    <div class="form-group mb-4">
                        <label for="slider-image-input" class="form-label">Profile Image</label>
                        <input type="file" name="image" id="slider-image-input" class="form-control">
                        <img src="" id="slider-imgsrc" class="img-responsive mt-2" style="max-width: 100px;">
                        @error('image')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary  waves-effect">
                        <span>SAVE</span>
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