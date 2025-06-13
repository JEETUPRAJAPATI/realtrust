@extends('backend.layouts.app')

@section('title', 'Change Password')

@push('styles')

@endpush


@section('content')


<div class="row clearfix">

<div class="col-xs-12">
    <div class="card">
        <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
            <h4 class="text-black mb-0">CHANGE PASSWORD</h4>
        </div>

        <!-- Card Body -->
        <div class="card-body mt-3">
            <form action="{{route('field_manager.changepassword')}}" method="POST">
                @csrf

                <!-- Current Password Field -->
                <div class="form-group form-float">
                    <div class="form-line {{ $errors->has('currentpassword') ? 'focused error' : '' }}">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="currentpassword" class="form-control" value="{{ old('currentpassword') }}">
                    </div>
                    @error('currentpassword')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- New Password Field -->
                <div class="form-group form-float">
                    <div class="form-line {{ $errors->has('newpassword') ? 'focused error' : '' }}">
                        <label class="form-label">New Password</label>
                        <input type="password" name="newpassword" class="form-control" value="{{ old('newpassword') }}">
                    </div>
                    @error('newpassword')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Confirm New Password Field -->
                <div class="form-group form-float">
                    <div class="form-line {{ $errors->has('newpassword_confirmation') ? 'focused error' : '' }}">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="newpassword_confirmation" class="form-control" value="{{ old('newpassword_confirmation') }}">
                    </div>
                    @error('newpassword_confirmation')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary btn-sm mt-4 waves-effect">
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
