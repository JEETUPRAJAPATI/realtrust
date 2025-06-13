@extends('backend.layouts.app')

@section('title', 'Add Mask Number')

@push('styles')

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">Add Owner's Mask Number</h4>
                <a href="{{ route('staff.owner.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>
            <div class="card-body mt-2">
                <form action="{{route('staff.owner.store.maskNumber')}}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group form-float">
                        <div class="form-line {{ $errors->has('ownerId') ? 'focused error' : '' }}">
                            <label class="form-label">Select Owner</label>
                            <select name="ownerId" class="form-control" id="maskNumber" required>
                                <option value="">Select</option>
                                @foreach($owners as $owner)
                                    <option value="{{ $owner->id }}" {{ old('ownerId') == $owner->id ? 'selected' : '' }}>
                                        {{ $owner->mobile_no }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('ownerId')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="form-group form-float">
                        <div class="form-line {{ $errors->has('mask_number') ? 'focused error' : '' }}">
                            <label class="form-label">Mask Number</label>
                            <input type="text" class="form-control" placeholder="Enter Mask Number" name="mask_number" value="{{ old('mask_number') }}" required>
                        </div>
                        @error('mask_number')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary mt-2 waves-effect">
                        <span>SAVE</span>
                    </button>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#maskNumber').select2();
    });
</script>
@endpush