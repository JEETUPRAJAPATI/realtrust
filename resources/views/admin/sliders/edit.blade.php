@extends('backend.layouts.app')

@section('title', 'Edit Slider')

@push('styles')
@endpush
@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <!-- Card Header -->
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">EDIT SLIDER</h4>
                <a href="{{ route('admin.sliders.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>

            <!-- Card Body -->
            <div class="card-body mt-3">
                <form action="{{ route('admin.sliders.update', $slider->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Title Field -->
                    <div class="mb-4">
                        <label for="slider-title" class="form-label fw-bold">Title</label>
                        <input type="text" id="slider-title" name="title" class="form-control" value="{{ $slider->title }}" placeholder="Enter slider title" required>
                    </div>

                    <!-- Image Upload Section -->
                    @if(Storage::disk('public')->exists('slider/'.$slider->image))
                    <div class="mb-4">
                        <label class="form-label fw-bold">Current Image</label>
                        <div class="text-center mb-3">
                            <img src="{{ Storage::url('slider/'.$slider->image) }}" id="slider-imgsrc-edit" class="img-fluid img-thumbnail rounded shadow-sm" alt="{{ $slider->title }}" style="max-width: 200px;">
                        </div>
                    </div>
                    @endif

                    <div class="mb-4">
                        <label for="slider-image-input-edit" class="form-label fw-bold">Upload New Image</label>
                        <input type="file" id="slider-image-input-edit" name="image" class="form-control" >
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-start mt-4">
                        <button type="submit" class="btn btn-primary waves-effect">
                            Update
                        </button>
                    </div>

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
    $('#slider-image-btn-edit').on('click', function() {
        $('#slider-image-input-edit').click();
    });
    $('#slider-image-input-edit').on('change', function() {
        showImage(this, '#slider-imgsrc-edit');
    });
</script>

@endpush