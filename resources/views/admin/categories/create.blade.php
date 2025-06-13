@extends('backend.layouts.app')

@section('title', 'Create Categories')

@push('styles')


@endpush


@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">CREATE CATEGORIES</h4>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>

            <!-- Card Body -->
            <div class="card-body mt-3">
                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="p-4 border rounded shadow-sm">
                    @csrf

                    <!-- Category Name Field -->
                    <div class="mb-3">
                        <label for="category-name" class="form-label">Category Name</label>
                        <input
                            type="text"
                            name="name"
                            id="category-name"
                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            value="{{ old('name') }}"
                            placeholder="Enter category name"
                            required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Image Upload Field -->
                    <div class="mb-3">
                        <label for="category-image" class="form-label">Upload Category Image (optional)</label>
                        <input
                            type="file"
                            name="image"
                            id="category-image"
                            class="form-control {{ $errors->has('image') ? 'is-invalid' : '' }}">
                        @error('image')
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



@endpush