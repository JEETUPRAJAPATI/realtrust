@extends('backend.layouts.app')

@section('title', 'Create Additional Details')

@push('styles')
@endpush

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">CREATE ADDITIONAL DETAIL</h4>
                <a href="{{ route('staff.additional-details.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>

            <div class="card-body mt-3">
                <form action="{{ route('staff.additional-details.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="detail-name" class="form-label">Detail Name</label>
                        <input
                            type="text"
                            name="name"
                            id="detail-name"
                            class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                            value="{{ old('name') }}"
                            placeholder="Enter detail name"
                            required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm mt-3">
                        Save
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@endpush