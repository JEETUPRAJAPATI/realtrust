@extends('backend.layouts.app')

@section('title', 'Edit FieldManager')

@push('styles')

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<!-- DateTimePicker CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">

@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header bg-indigo">
                <h2>
                    EDIT FieldManager
                    <a href="{{route('admin.schedule_visit.index')}}" class="waves-effect waves-light btn btn-info right headerightbtn">
                        <i class="material-icons left">arrow_back</i>
                        <span>BACK</span>
                    </a>
                </h2>
            </div>
            <div class="body">
                <form action="{{route('admin.schedule_visit.update',$visiterInfo->id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <!-- Properties Dropdown -->
                    <div class="form-group form-float">
                        <label class="form-label">Properties</label>
                        <div class="form-line">
                            <select name="property_id" id="property-select" class="form-control select2" disabled>
                                <option value="">Select Properties</option>
                                @foreach($properties as $prop)
                                <option value="{{ $prop->id }}" {{ $visiterInfo->property_id == $prop->unique_id ? 'selected' : ''  }}>
                                    {{ $prop->unique_id }} - {{ $prop->title }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @error('property_id')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Owner Dropdown -->
                    <div class="form-group form-float">
                        <label class="form-label">Owner</label>
                        <div class="form-line">
                            <select id="owner-select" name="owner_id" class="form-control select2" disabled>
                                <option value="">Select Owner</option>
                                @foreach($owners as $owner)
                                <option value="{{ $owner->id }}" {{ $visiterInfo->owner_id == $owner->id ? 'selected' : ''  }}>
                                    {{ $owner->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @error('owner_id')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Field Manager Dropdown -->
                    <div class="form-group form-float">
                        <label class="form-label">Field Manager</label>
                        <div class="form-line">
                            <select name="field_manager_id" class="form-control select2">
                                <option value="">Select Field Manager</option>
                                @foreach($fieldManagers as $manager)
                                <option value="{{ $manager->id }}" {{ $visiterInfo->field_manager_id == $manager->id ? 'selected' : '' }}>
                                    {{ $manager->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @error('field_manager_id')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Users Dropdown -->
                    <div class="form-group form-float">
                        <label class="form-label">Users</label>
                        <div class="form-line">
                            <select name="user_id" class="form-control select2" disabled>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $visiterInfo->user_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @error('user_id')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Timing Field -->
                    <div class="form-group form-float">
                        <label class="form-label">Timing</label>
                        <div class="form-line">
                            <input type="text" name="timing" class="form-control datetimepicker" value="{{ $visiterInfo->timing}}">
                        </div>
                        @error('timing')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-indigo btn-lg m-t-15 waves-effect">
                        <i class="material-icons">update</i>
                        <span>Update</span>
                    </button>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection


@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script>
    $('.select2').select2();
    $(document).ready(function() {

        $('.datetimepicker').datetimepicker({
            format: 'Y-m-d H:i', // Format for date and time
            step: 30, // Interval for time picker in minutes
            minDate: 0, // Disallow past dates
            autoclose: true,
            todayHighlight: true
        });

    });
</script>

@endpush