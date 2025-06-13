@extends('backend.layouts.app')

@section('title', 'Schedule Visit')

@push('styles')

<link rel="stylesheet" href="{{asset('backend/plugins/bootstrap-select/css/bootstrap-select.css')}}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.5/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<!-- DateTimePicker CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">


@endpush


@section('content')

<div class="block-header"></div>

<div class="row clearfix">
    <form action="{{ route('admin.schedule_visit.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header bg-indigo">
                    <h2>Schedule Visit</h2>
                </div>
                <div class="body">
                    <div class="form-group form-float">
                        <label class="form-label">Properties</label>
                        <div class="form-line">
                            <select name="properties" id="property-select" class="form-control select2">
                                <option value="">Select Properties</option>
                                @foreach($property as $prop)
                                <option value="{{ $prop->unique_id }}">{{ $prop->unique_id }} - {{ $prop->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('properties')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group form-float">
                        <label class="form-label">Owner</label>
                        <div class="form-line">
                            <select id="owner-select" name="owner" class="form-control select2">
                                <option value="">Select Owner</option>
                            </select>
                        </div>
                        @error('owner')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group form-float">
                        <label class="form-label">Field Manager</label>
                        <div class="form-line">
                            <select name="field_manager_id" class="form-control select2">
                                <option value="">Select Field Manager</option>
                                @foreach($fieldManager as $manager)
                                <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('field_manager_id')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group form-float">
                        <label class="form-label">Users</label>
                        <div class="form-line">
                            <select name="users" class="form-control select2">
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('users')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>





                    <div class="form-group form-float">
                        <label class="form-label">Select Date and Time</label>
                        <div class="form-line">
                            <input type="text" class="form-control datetimepicker" name="timing" value="{{ old('timing') }}">
                        </div>
                        @error('timing')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                    <a href="{{route('admin.schedule_visit.index')}}" class="btn btn-danger btn-lg m-t-15 waves-effect">
                        <i class="material-icons left">arrow_back</i>
                        <span>BACK</span>
                    </a>
                    <button type="submit" class="btn btn-indigo btn-lg m-t-15 waves-effect">
                        <i class="material-icons">save</i>
                        <span>SAVE</span>
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>


@endsection


@push('scripts')

<script src="{{ asset('backend/plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>
<script src="{{asset('backend/plugins/tinymce/tinymce.js')}}"></script>
<!-- Bootstrap Datepicker JS -->
<script src="{{asset('backend/plugins/tinymce/tinymce.js')}}"></script>
<!-- DateTimePicker JS -->
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
    $('#property-select').change(function() {
        var propertyId = $(this).val();
        var $ownerSelect = $('#owner-select');
        $ownerSelect.empty().append('<option value="">Select Owner</option>');

        if (propertyId) {
            var url = "{{ route('admin.schedule_visit.property') }}";

            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    propertyId: propertyId
                },
                dataType: 'json',
                success: function(data) {
                    if (data) {
                        $ownerSelect.append('<option selected value="' + data.id + '">' + data.name + '</option>');
                    }
                },
                error: function(xhr) {
                    console.log('Error:', xhr.responseText);
                }
            });
        }
    });
</script>
@endpush