@extends('backend.layouts.app')

@section('title', 'Edit Feature')

@push('styles')


@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">EDIT AMENITIES</h4>
                <a href="{{ route('admin.amenities.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>
            <!-- Card Body -->
            <div class="card-body mt-3">
                <form action="{{route('admin.amenities.update',$feature->id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group form-float mb-3">
                        <div class="form-line">
                            <label class="form-label">Amenities</label>
                            <input type="text" name="name" placeholder="Enter Amenities" class="form-control" value="{{ old('name', $feature->name) }}">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>


                    @if(Storage::disk('public')->exists('amenities/'.$feature->image))
                    <div class="form-group mb-3">
                        <img src="{{Storage::url('amenities/'.$feature->image)}}" id="user-imgsrc-edit" alt="{{$feature->name}}" class="img-fluid img-thumbnail rounded"
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
    $('#user-image-btn-edit').on('click', function() {
        $('#user-image-input-edit').click();
    });
    $('#user-image-input-edit').on('change', function() {
        showImage(this, '#user-imgsrc-edit');
    });
</script>

@endpush