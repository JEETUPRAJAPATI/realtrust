@extends('backend.layouts.app')

@section('title', 'Add Society')

@push('styles')


@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header bg-indigo">
                <h2>
                    Add SOCIETY
                    <a href="{{route('staff.society.index')}}" class="waves-effect waves-light btn btn-info right headerightbtn">
                        <i class="material-icons left">arrow_back</i>
                        <span>BACK</span>
                    </a>
                </h2>
            </div>
            <div class="body">
                <form action="{{route('staff.society.store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group form-float">
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
                        </div>
                    </div>
                    <div class="form-group form-float">
                        <div class="form-line {{ $errors->has('name') ? 'focused error' : '' }}">
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                            <label class="form-label">Name</label>
                        </div>
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-indigo btn-lg m-t-15 waves-effect">
                        <i class="material-icons">save</i>
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