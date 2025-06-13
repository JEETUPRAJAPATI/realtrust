@extends('backend.layouts.app')

@section('title', 'Add Owner')

@push('styles')


@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">ADD OWNER</h4>
                <a href="{{ route('staff.owner.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>
            <div class="card-body mt-2">
                <form action="{{route('staff.owner.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group form-float">
                        <div class="form-line {{ $errors->has('name') ? 'focused error' : '' }}">
                            <label class="form-label">Name</label>
                            <input type="text" placeholder=" Enter Owner's Name " name="name" class="form-control" value="{{ old('name') }}">
                        </div>
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group form-float">
                        <div class="form-line {{ $errors->has('email') ? 'focused error' : '' }}">
                            <label class="form-label">Email</label>
                            <input type="text" placeholder=" Enter Owner's Email" name="email" class="form-control" value="{{ old('email') }}">
                        </div>
                        @error('email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group form-float">
                        <div class="form-line {{ $errors->has('mobile') ? 'focused error' : '' }}">
                            <label class="form-label">Mobile</label>
                            <input type="text" placeholder=" Enter Owner's Mobile Number " name="mobile" class="form-control" value="{{ old('mobile') }}">
                        </div>
                        @error('mobile')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!--<div class="form-group form-float">-->
                    <!--    <div class="form-line {{ $errors->has('password') ? 'focused error' : '' }}">-->
                    <!--        <label class="form-label">Password</label>-->
                    <!--        <input type="password" placeholder=" Enter Password" name="password" class="form-control">-->
                    <!--    </div>-->
                    <!--    @error('password')-->
                    <!--    <span class="text-danger">{{ $message }}</span>-->
                    <!--    @enderror-->
                    <!--</div>-->
                    <!-- Image Upload -->
                    <div class="form-group mb-4">
                        <label for="slider-image-input" class="form-label">Profile Image</label>
                        <input type="file" name="image" id="slider-image-input" class="form-control">
                        <img src="" id="slider-imgsrc" class="img-responsive mt-2 img-thumbnail" style="max-width: 200px;">
                        @error('image')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary mt-2 waves-effect">
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
                    $(imgID).attr('width', 100);
                    $(imgID).attr('alt', fileInput.files[0].name);
                }
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
        // $('#slider-image-btn').on('click', function() {
        //     $('#slider-image-input').click();
        // });
        $('#slider-image-input').on('change', function() {
            showImage(this, '#slider-imgsrc');
        });
    })
</script>

@endpush