@extends('backend.layouts.app')

@section('title', 'Edit Tags')

@push('styles')


@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="body">
                <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                    <h4 class="text-black mb-0">EDIT TAG</h4>
                    <a href="{{ route('admin.tags.index') }}" class="btn btn-light">
                        <i class="bi bi-arrow-left-short"></i> BACK
                    </a>
                </div>

                <!-- Card Body -->
                <div class="card-body mt-3">
                    <form action="{{ route('admin.tags.update', $tag->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Tag Name Field -->
                        <div class="mb-3">
                            <label for="tag-name" class="form-label">Tag Name</label>
                            <input
                                type="text"
                                name="name"
                                id="tag-name"
                                class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                value="{{ old('name', $tag->name) }}"
                                placeholder="Enter tag name"
                                required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Update Button -->
                        <button type="submit" class="btn btn-primary btn-sm mt-3">
                            Update
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    @endsection


    @push('scripts')



    @endpush