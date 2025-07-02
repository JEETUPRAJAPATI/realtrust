@extends('backend.layouts.app')

@section('title', 'Edit Slider')

@push('styles')


@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">EDIT STAFF</h4>
                <a href="{{ route('admin.staff.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>

            <!-- Card Body -->
            <div class="card-body mt-3">
                <form action="{{route('admin.staff.update',$staff->id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group form-float mb-3">
                        <label class="form-label">Name</label>
                        <div class="form-line">
                            <input type="text" name="name" placeholder="Enter Name" class="form-control" value="{{$staff->name}}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter Email" class="form-control" value="{{ old('email', $staff->email) }}" placeholder="Enter email">
                        @error('email')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('mobile') ? 'focused error' : '' }}">
                            <label class="form-label">Mobile</label>
                            <input type="tel" placeholder="Enter Mobile" name="mobile" class="form-control" value="{{ old('mobile',$staff->mobile_no) }}">
                        </div>
                        @error('mobile')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    @if(Storage::disk('public')->exists('staff/'.$staff->image))
                    <div class="form-group">
                        <img src="{{Storage::url('staff/'.$staff->image)}}" id="staff-imgsrc" alt="{{$staff->name}}" class="img-thumbnail"
                            style="max-width: 200px;">
                    </div>
                    @endif
                    <div class="mb-3">
                        <label for="staff-image-input" class="form-label">Profile Image</label>
                        <!-- <img src="{{ asset('assets/img/defaultprofile.png') }}" id="staff-imgsrc" class="img-thumbnail d-block mb-3" style="max-width: 150px; display: none;" alt="Staff Image"> -->
                        <input type="file" name="image" id="staff-image-input" class="form-control" accept="image/*">
                        @error('image')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary  m-t-15 waves-effect">
                        <!-- <i class="material-icons">update</i> -->
                        <span>Update</span>
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