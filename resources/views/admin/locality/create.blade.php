@extends('backend.layouts.app')

@section('title', 'Add Locality')

@push('styles')


@endpush


@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0"> Add Locality</h4>
                <a href="{{ route('admin.locality.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>

            <!-- Card Body -->
            <div class="card-body mt-3">
                <form action="{{ route('admin.locality.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- State (Disabled) -->
                    <div class="mb-3">
                        <label for="state" class="form-label">State</label>
                        <input type="text"
                            class="form-control"
                            name="state"
                            id="state"
                            value="{{ $stateWithCity->state->state_name }}"
                            disabled>
                    </div>

                    <!-- City (Disabled) -->
                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text"
                            class="form-control"
                            name="city"
                            id="city"
                            value="{{ $stateWithCity->city_name }}"
                            disabled>
                    </div>

                    <!-- Locality Name -->
                    <div class="mb-3">
                        <label for="locality" class="form-label">Locality Name</label>
                        <input type="text"
                            name="locality"
                            id="locality"
                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            value="{{ old('locality') }}"
                            placeholder="Enter locality name"
                            required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-sm m-t-15 waves-effect">
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