@extends('backend.layouts.app')

@section('title', 'Add Locality')

@push('styles')


@endpush


@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header bg-indigo">
                <h2>
                    Add Locality
                    <a href="{{ route('staff.locality.index') }}" class="waves-effect waves-light btn btn-info right headerightbtn">
                        <i class="material-icons left">arrow_back</i>
                        <span>BACK</span>
                    </a>
                </h2>
            </div>
            <div class="body">
                <form action="{{ route('staff.locality.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                        <!-- State (Disabled) -->
                        <div class="form-group form-float">
                        <div class="form-line">
                            <input type="text" class="form-control" name="state" value="{{ $stateWithCity->state->state_name }}" disabled>
                            <label class="form-label">State</label>
                        </div>
                    </div>

                    <!-- City (Disabled) -->
                    <div class="form-group form-float">
                        <div class="form-line">
                            <input type="text" class="form-control" name="city" value="{{ $stateWithCity->city_name }}" disabled>
                            <label class="form-label">City</label>
                        </div>
                    </div>
                    <!-- Locality Name -->
                    <div class="form-group form-float">
                        <div class="form-line {{ $errors->has('name') ? 'focused error' : '' }}">
                            <input type="text" name="locality" class="form-control" value="{{ old('name') }}">
                            <label class="form-label">Locality Name</label>
                        </div>
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>



                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-indigo btn-lg m-t-15 waves-effect">
                        <i class="material-icons">save</i>
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