@extends('backend.layouts.app')

@section('title', 'Edit Locality')

@push('styles')


@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header bg-indigo">
                <h2>
                    EDIT LOCALITY
                    <a href="{{route('staff.locality.index')}}" class="waves-effect waves-light btn btn-info right headerightbtn">
                        <i class="material-icons left">arrow_back</i>
                        <span>BACK</span>
                    </a>
                </h2>
            </div>
            <div class="body">
                <form action="{{route('staff.locality.update',$localities->id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                       <!-- State (Disabled) -->
                       <div class="form-group form-float">
                        <div class="form-line">
                            <input type="text" class="form-control" name="state" value="{{ $localities->state->state_name }}" disabled>
                            <label class="form-label">State</label>
                        </div>
                    </div>

                    <!-- City (Disabled) -->
                    <div class="form-group form-float">
                        <div class="form-line">
                            <input type="text" class="form-control" name="city" value="{{ $localities->city->city_name }}" disabled>
                            <label class="form-label">City</label>
                        </div>
                    </div>
                    <!-- Locality Name -->
                    <div class="form-group form-float">
                        <div class="form-line {{ $errors->has('name') ? 'focused error' : '' }}">
                            <input type="text" name="locality" class="form-control" value="{{ old('name',$localities->name) }}">
                            <label class="form-label">Locality Name</label>
                        </div>
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                    <button type="submit" class="btn btn-indigo btn-lg m-t-15 waves-effect">
                        <i class="material-icons">update</i>
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