@extends('backend.layouts.app')

@section('title', 'Edit Slider')

@push('styles')


@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">EDIT {{ $admin->name }}</h4>
                <a href="{{ route('admin.admin-list.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>

            <!-- Card Body -->
            <div class="card-body mt-3">
                <form action="{{ route('admin.admin-list.update', $admin->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Name Input -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $admin->name) }}" placeholder="Enter name">
                        @error('name')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Image Preview (if exists) -->
                    @if(Storage::disk('public')->exists('admin/' . $admin->image))
                    <div class="form-group mb-3">
                        <img src="{{ Storage::url('admin/' . $admin->image) }}" id="admin-imgsrc-edit" alt="{{ $admin->name }}" class="img-fluid img-thumbnail rounded"
                        style="max-width: 200px;">
                    </div>
                    @endif

                    <!-- Image Upload -->
                    <div class="mb-3">
                        <label for="admin-image-input-edit" class="form-label">Upload Image</label>
                        <input type="file" name="image" id="admin-image-input-edit" class="form-control">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary">
                        Update
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
    $('#admin-image-btn-edit').on('click', function() {
        $('#admin-image-input-edit').click();
    });
    $('#admin-image-input-edit').on('change', function() {
        showImage(this, '#admin-imgsrc-edit');
    });
</script>

@endpush