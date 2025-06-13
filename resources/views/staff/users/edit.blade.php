@extends('backend.layouts.app')

@section('title', 'Edit USER')

@push('styles')


@endpush


@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">


            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">EDIT USER</h4>
                <a href="{{ route('staff.user.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>

            <!-- Card Body -->
            <div class="card-body mt-3">
                <form action="{{ route('staff.user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Name Input -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" placeholder="Enter name">
                        @error('name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('mobile') ? 'focused error' : '' }}">
                            <label class="form-label">Mobile</label>
                            <input type="tel" placeholder="Enter Mobile" name="mobile" class="form-control" value="{{ old('mobile',$user->mobile_no) }}" disabled>
                        </div>
                        @error('mobile')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Image Preview -->
                    @if($user->image !== NULL)
                    <div class="mb-3">
                        <img src="{{Storage::url('users/'.$user->image)}}" id="user-imgsrc-edit" alt="{{ $user->name }}"  class="img-thumbnail"
                        style="max-width: 200px;">
                    </div>
                    @endif
                    

                    <!-- Image Upload -->
                    <div class="mb-3">
                        <label for="user-image-input-edit" class="form-label">Upload New Image</label>
                        <input type="file" id="user-image-input-edit" name="image" class="form-control">
                        <!-- <div class="mt-2">
                            <button type="button" class="btn btn-secondary btn-sm" id="user-image-btn-edit">
                                <i class="bi bi-upload"></i> Choose File
                            </button>
                        </div> -->
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">
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
    $('#user-image-btn-edit').on('click', function() {
        $('#user-image-input-edit').click();
    });
    $('#user-image-input-edit').on('change', function() {
        showImage(this, '#user-imgsrc-edit');
    });
</script>

@endpush