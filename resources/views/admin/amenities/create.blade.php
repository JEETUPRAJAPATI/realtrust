@extends('backend.layouts.app')
@section('title', 'Create Feature')
@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0"> CREATE AMENITIES</h4>
                <a href="{{ route('admin.amenities.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>
            <div class="card-body mt-3">
                <form action="{{route('admin.amenities.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group form-float mb-2">
                        <div class="form-line">
                            <label class="form-label">Amenities</label>
                            <input type="text" name="name" placeholder="Enter Amenities" class="form-control" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group  mb-2">
                        <label for="slider-image-input" class="form-label">Amenities Image</label>
                        <div class="text-center">
                            <img id="slider-imgsrc" src="" class="img-fluid rounded shadow-sm" alt="Preview" style="max-width: 200px; display: none;">
                        </div>
                        <input type="file" id="slider-image-input" name="image" class="form-control" required>
                        @error('image')
                                <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary waves-effect">
                        <span>SAVE</span>
                    </button>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection