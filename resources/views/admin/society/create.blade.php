@extends('backend.layouts.app')

@section('title', 'Add Society')

@push('styles')


@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">ADD SOCIETY</h4>
                <a href="{{ route('admin.society.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>

            <!-- Card Body -->
            <div class="card-body mt-3">
                <form action="{{ route('admin.society.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Locality Selection -->
                    <div class="form-group form-float mb-4">
                        <div class="form-line">
                            <label class="form-label">Locality</label>
                            <select name="locality" class="form-control show-tick">
                                <option value="">-- Please select --</option>
                                @foreach ($localities as $locality)
                                <option value="{{ $locality->id }}" {{ old('locality') == $locality->id ? 'selected' : '' }}>
                                    {{ $locality->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('locality')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Map Input -->
                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('embeded_map') ? 'focused error' : '' }}">
                            <label class="form-label">Embede Map</label>
                            <input type="text" name="embeded_map" class="form-control" value="{{ old('embeded_map') }}"
                            placeholder="Paste Google Maps embed code here">
                        </div>
                        @error('embeded_map')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Name Input -->
                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('name') ? 'focused error' : '' }}">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                            placeholder="Enter Society Name">
                        </div>
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group text-left">
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
@endpush