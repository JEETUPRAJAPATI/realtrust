@extends('backend.layouts.app')

@section('title', 'Show Property')

@push('styles')

<link rel="stylesheet" href="{{asset('backend/plugins/bootstrap-select/css/bootstrap-select.css')}}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.5/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<!-- DateTimePicker CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">


@endpush
@section('content')

<div class="block-header"></div>
<div class="container-fluid">
    <div class="block-header">
        <h2>FORM WIZARD</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>BASIC EXAMPLE - HORIZONTAL LAYOUT</h2>
                    <ul class="header-dropdown m-r--5">
                        <li class="dropdown">
                            <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">more_vert</i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="javascript:void(0);">Action</a></li>
                                <li><a href="javascript:void(0);">Another action</a></li>
                                <li><a href="javascript:void(0);">Something else here</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <div id="wizard_horizontal">
                        <h2>Property Details</h2>
                        <section>
                            <p><strong>Title:</strong> {{$visiterInfo->property->title }}</p>
                            <p><strong>Type:</strong> {{$visiterInfo->property->type }}</p>
                            <p><strong>Location:</strong> {{$visiterInfo->property->city }}, {{$visiterInfo->property->locality }}</p>
                            <p><strong>Price:</strong> ${{ number_format($visiterInfo->property->price, 2) }}</p>
                            <p><strong>Description:</strong> {{$visiterInfo->property->description }}</p>
                        </section>

                        <h2>Owner Details</h2>
                        <section>
                            <p><strong>Name:</strong> {{ $visiterInfo->property->owner->name }}</p>
                            <p><strong>Email:</strong> {{ $visiterInfo->property->owner->email }}</p>
                            <p><strong>Phone:</strong> {{ $visiterInfo->property->owner->mobile_no }}</p>
                        </section>

                        <h2>User Details</h2>
                        <section>
                            <p><strong>User Name:</strong> {{ $visiterInfo->full_name }}</p>
                            <p><strong>Email:</strong> {{ $visiterInfo->email }}</p>
                            <p><strong>Comapny:</strong> {{ $visiterInfo->company_name }}</p>
                        </section>

                        <h2>Scheduled Visit</h2>
                        <section>


                            <div class="row clearfix">
                                <form action="{{ route('staff.schedule_visit.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="card">
                                            <div class="header bg-indigo">
                                                <h2>Schedule Visit</h2>
                                            </div>
                                            <div class="body">
                                                <div class="form-group form-float mb-2">
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

                                                <div class="form-group form-float mb-2">
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
                                                <div class="form-group form-float mb-2">
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
                                                <div class="form-group form-float mb-2">
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





                                                <div class="form-group form-float mb-2">
                                                    <label class="form-label">Select Date and Time</label>
                                                    <div class="form-line">
                                                        <input type="text" class="form-control datetimepicker" name="timing" value="{{ old('timing') }}">
                                                    </div>
                                                    @error('timing')
                                                    <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>


                                                <a href="{{route('staff.schedule_visit.index')}}" class="btn btn-danger btn-lg m-t-15 waves-effect mt-3">
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


                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    $(document).ready(function() {
        $('.select2').select2();

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
            var url = "{{ route('staff.schedule_visit.property') }}";

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