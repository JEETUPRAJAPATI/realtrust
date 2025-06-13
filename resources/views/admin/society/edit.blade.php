@extends('backend.layouts.app')

@section('title', 'Edit Society')

@push('styles')


@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0"> EDIT SOCIETY</h4>
                <a href="{{ route('admin.society.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>

            <div class="card-body mt-3">
                <form action="{{ route('admin.society.update', $socity->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Locality (Read-Only) -->
                    <div class="form-group form-float mb-4">
                        <div class="form-line">
                            <label class="form-label">Locality</label>
                            <input type="text" name="locality" class="form-control" value="{{ old('locality', $socity->locality->name) }}" disabled>
                            @error('locality')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Name Input -->
                    <div class="form-group form-float mb-4">
                        <label class="form-label">Name</label>
                        <div class="form-line {{ $errors->has('name') ? 'focused error' : '' }}">
                            <input type="text" name="name" class="form-control" value="{{ old('name', $socity->name) }}"
                                placeholder="Enter Society Name">
                        </div>
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Map Input -->
                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('embeded_map') ? 'focused error' : '' }}">
                            <label class="form-label">Embede Map</label>
                            <input type="text" name="embeded_map" class="form-control" value="{{ old('embeded_map',$socity->embeded_map) }}"
                                placeholder="Paste Google Maps embed code here">
                        </div>
                        @error('embeded_map')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group text-left">
                        <button type="submit" class="btn btn-primary btn-lg m-t-15 waves-effect">
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
    $('#owner-image-btn-edit').on('click', function() {
        $('#owner-image-input-edit').click();
    });
    $('#owner-image-input-edit').on('change', function() {
        showImage(this, '#owner-imgsrc-edit');
    });
</script>

@endpush