@extends('backend.layouts.app')

@section('title', 'Create Sliders')

@push('styles')

<link rel="stylesheet" href="{{asset('backend/plugins/bootstrap-select/css/bootstrap-select.css')}}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.5/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

@endpush


@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <!-- Card Header -->
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">CREATE SLIDER</h4>
                <a href="{{ route('admin.sliders.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>

            <!-- Card Body -->
            <div class="card-body mt-3">
                <form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Title Field -->
                    <div class="mb-4">
                        <label for="slider-title" class="form-label fw-bold">Title</label>
                        <input type="text" id="slider-title" name="title" class="form-control" placeholder="Enter slider title" required>
                    </div>

                    <!-- Image Upload Section -->
                    <div class="mb-4">
                        <label for="slider-image-input" class="form-label fw-bold">Slider Image</label>
                        <div class="text-center mb-3">
                            <img id="slider-imgsrc" src="" class="img-fluid rounded shadow-sm" alt="Preview" style="max-width: 200px; display: none;">
                        </div>
                        <input type="file" id="slider-image-input" name="image" class="form-control" required>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-start mt-4">
                        <button type="submit" class="btn btn-primary waves-effect">
                            SAVE
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>


@endsection


@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.5/js/fileinput.min.js"></script>

<script src="{{ asset('backend/plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>

<!-- Bootstrap Datepicker JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

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
    $('#slider-image-btn').on('click', function() {
        $('#slider-image-input').click();
    });
    $('#slider-image-input').on('change', function() {
        showImage(this, '#slider-imgsrc');
    });

    $(function() {
        $("#input-id").fileinput();
    });
</script>

@endpush