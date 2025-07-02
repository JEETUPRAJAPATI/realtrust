@extends('backend.layouts.app')

@section('title', 'Edit Owner')

@push('styles')


@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">


            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0"> EDIT OWNER</h4>
                <a href="{{ route('admin.owner.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>

            <!-- Card Body -->
            <div class="card-body mt-3">
                <form action="{{route('admin.owner.update',$owners->id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group form-float mb-4">
                        <label class="form-label">Name</label>
                        <div class="form-line">
                            <input type="text" name="name" class="form-control" placeholder="Enter Name" value="{{ old('name', $owners->name) }}">
                        </div>
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group form-float mb-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter Email" class="form-control" value="{{ old('email', $owners->email) }}" placeholder="Enter email">
                        @error('email')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('mobile') ? 'focused error' : '' }}">
                            <label class="form-label">Mobile</label>
                            <input type="tel" placeholder="Enter Mobile" placeholder="Enter Mobile" name="mobile" class="form-control" value="{{ old('mobile',$owners->mobile_no) }}">
                        </div>
                        @error('mobile')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                    @if(Storage::disk('public')->exists('owners/'.$owners->image))
                    <div class="form-group mb-3">
                        <img src="{{Storage::url('owners/'.$owners->image)}}" alt="{{$owners->title}}" class="img-fluid img-thumbnail rounded"
                            style="max-width: 200px;">
                    </div>
                    @endif


                    <div class="mb-4">
                        <label for="slider-image-input-edit" class="form-label">Upload New Image</label>
                        <input type="file" id="slider-image-input-edit" name="image" class="form-control">
                        @error('image')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary  waves-effect">
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