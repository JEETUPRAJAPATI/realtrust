@extends('backend.layouts.app')

@section('title', 'Profile')

@push('styles')

@endpush


@section('content')

<div class="block-header"></div>

<div class="row clearfix">

    <div class="col-xs-12">
        <div class="card">

            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">Profile Setting</h4>
            </div>

            <!-- Card Body -->
            <div class="card-body mt-3">
                <form action="{{route('admin.profile')}}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Name Field -->
                    <div class="form-group form-float">
                        <div class="form-line {{ $errors->has('name') ? 'focused error' : '' }}">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $profile->name }}">
                        </div>
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email Field -->
                    <div class="form-group form-float">
                        <div class="form-line {{ $errors->has('email') ? 'focused error' : '' }}">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $profile->email }}">
                        </div>
                        @error('email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Profile Image Upload -->
                    <div class="form-group">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control">
                        @if($profile->image)
                        <div class="mt-2">
                            <img src="{{ Storage::url('admin/'.$profile->image) }}" alt="Profile Image" class="img-responsive img-rounded" width="100">
                        </div>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-sm  waves-effect">
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
        $('#profile-image-btn').on('click', function() {
            $('#profile-image-input').click();
        });
        $('#profile-image-input').on('change', function() {
            showImage(this, '#profile-imgsrc');
        });
    })
</script>

@endpush