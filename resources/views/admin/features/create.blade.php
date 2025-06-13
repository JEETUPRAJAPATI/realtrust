@extends('backend.layouts.app')

@section('title', 'Create Feature')

@push('styles')


@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <!-- Card Header -->
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">CREATE FEATURE</h4>
                <a href="{{ route('admin.features.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>

            <!-- Card Body -->
            <div class="card-body mt-3">
                <form action="{{ route('admin.features.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Feature Name Input -->
                    <div class="form-group form-float">
                        <div class="form-line">
                            <label class="form-label">Feature Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter Feature Name" required>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Feature Image Upload -->
                    <div class="form-group">
                        <label for="slider-image-input" class="form-label">Feature Image</label>
                        <input type="file" name="image" id="slider-image-input" class="form-control" accept="image/*" required>
                        @error('image')
                                <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-start mt-4">
                        <button type="submit" class="btn btn-primary btn-lg  waves-effect">
                            <span>SAVE</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>


@endsection


@push('scripts')



@endpush