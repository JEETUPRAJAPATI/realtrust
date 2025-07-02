@extends('backend.layouts.app')

@section('title', 'Show Property')


@push('styles')

<link rel="stylesheet" href="{{asset('backend/plugins/bootstrap-select/css/bootstrap-select.css')}}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.5/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<!-- DateTimePicker CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css">


@endpush

<style>
    .card {
        box-shadow: none !important;
    }
</style>
@section('content')

<div class="block-header"></div>
<div class="row clearfix">
    <!-- Property Details Section -->
    <div class="col-lg-8 col-md-4 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">PROPERTY DETAIL</h4>
            </div>

            <!-- Card Body -->

            <div class="card-body mt-3">
                <p class="mb-0">
                    <strong>{{ $visiterInfo->property->title }}</strong>
                </p>
                <p class="text-muted">
                    <small>Posted by <strong>{{ $visiterInfo->property->owner->name ?? 'N/A' }}</strong> on {{ $visiterInfo->property->created_at->toFormattedDateString() }}</small>
                </p>
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Price:</strong>
                        <span class="right">{{$visiterInfo->property->price}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Bedroom:</strong>
                        <span class="right">{{$visiterInfo->property->bedroom}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Bathroom:</strong>
                        <span class="right">{{$visiterInfo->property->bathroom}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>City:</strong>
                        <span class="right">
                            {{ $city->city_name ?? 'N/A' }}
                        </span>
                    </li>
                    <li class="list-group-item">
                        <strong>Locality:</strong>
                        <span class="right">
                            {{ $visiterInfo->property->localities->name ?? 'N/A' }}
                        </span>
                    </li>
                    <li class="list-group-item">
                        <strong>Society Name:</strong>
                        <span class="right">
                            {{ $visiterInfo->property->society->name ?? 'N/A' }}
                        </span>
                    </li>
                </ul>
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Type:</strong>
                        <span class="right">{{$visiterInfo->property->type}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Bhk:</strong>
                        <span class="right">{{$visiterInfo->property->bhk}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Area:</strong>
                        <span class="right">{{$visiterInfo->property->area}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Furnish Type:</strong>
                        <span class="right">{{$visiterInfo->property->furnish_type}}</span>
                    </li>

                    <li class="list-group-item">
                        <strong>Available For:</strong>
                        <span class="right">{{$visiterInfo->property->available_for}}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">User</h4>
            </div>

            <div class="card-body mt-3">
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Name:</strong>
                        <span class="right">{{$visiterInfo->users->name}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Email:</strong>
                        <span class="right">{{$visiterInfo->users->email}}</span>
                    </li>
                    <!--<li class="list-group-item">-->
                    <!--    <strong>Mobile:</strong>-->
                    <!--    <span class="right">{{$visiterInfo->users->mobile_no}}</span>-->
                    <!--</li>-->
                </ul>

            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">Owner</h4>
            </div>

            <div class="card-body mt-3">
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Name:</strong>
                        <span class="right">{{$visiterInfo->property->owner->name ?? 'N/A'}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Email:</strong>
                        <span class="right">{{$visiterInfo->property->owner->email ?? 'N/A'}}</span>
                    </li>

                    <li class="list-group-item">
                        <strong>Mobile:</strong>

                        <!--<span class="right">{{$visiterInfo->property->owner->mobile_no ?? 'N/A'}}</span>-->
                        <button type="button" id="makeCall" class="btn btn-primary" data-bs-toggle="modal">
                            Call Now
                        </button>
                        <input type="hidden" class="form-control" id="customerNumber" name="customer_number" value="{{ $visiterInfo->property->owner->mobile_no }}" required>
                        <input type="hidden" class="form-control" id="agentNumber" name="agent_number" value="{{ auth('staff')->user()->mobile_no }}" required>
                    </li>
                    <li class="list-group-item">
                        <strong>Manual Confirm Timing:</strong>
                        <span class="right">
                            <a href="https://admin.realtrust.in/conform-timing/{{ $visiterInfo->property->unique_id ?? '' }}" target="_blank" rel="noopener noreferrer">
                                Click Here
                            </a>
                        </span>
                    </li>

                    <li class="list-group-item">
                        @if ($visiterInfo->conform_timing && $visiterInfo->conform_timing->timing)
                        <p>
                            <strong>Timing:</strong>
                            <span class="right">
                                @if ($visiterInfo->conform_timing && $visiterInfo->conform_timing->timing)
                                @php
                                // Split the timing string into start and end times
                                [$startDatetime, $endDatetime] = explode(' - ', $visiterInfo->conform_timing->timing);

                                // Format the start and end times using Carbon
                                $formattedStart = Carbon\Carbon::createFromFormat('m/d/Y h:i A', $startDatetime)->format('l, F j, Y \a\t g:i A');
                                $formattedEnd = Carbon\Carbon::createFromFormat('m/d/Y h:i A', $endDatetime)->format('l, F j, Y \a\t g:i A');
                                @endphp
                                {{ $formattedStart }} - {{ $formattedEnd }}
                                @else
                                No timing available
                                @endif
                            </span>
                        </p>
                        @else
                        <p> <strong>Timing:</strong>
                            <a href="{{route('staff.owner.send-time-confirmation',$visiterInfo->property->unique_id)}}" class="btn btn-success waves-effect">
                                <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium css-9gwpq2" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="WhatsAppIcon" style="user-select: none;width: 24px;display: block;flex-shrink: 0;fill: currentcolor;font-size: 210px;color: rgb(255, 255, 255);">
                                    <path d="M16.75 13.96c.25.13.41.2.46.3.06.11.04.61-.21 1.18-.2.56-1.24 1.1-1.7 1.12-.46.02-.47.36-2.96-.73-2.49-1.09-3.99-3.75-4.11-3.92-.12-.17-.96-1.38-.92-2.61.05-1.22.69-1.8.95-2.04.24-.26.51-.29.68-.26h.47c.15 0 .36-.06.55.45l.69 1.87c.06.13.1.28.01.44l-.27.41-.39.42c-.12.12-.26.25-.12.5.12.26.62 1.09 1.32 1.78.91.88 1.71 1.17 1.95 1.3.24.14.39.12.54-.04l.81-.94c.19-.25.35-.19.58-.11l1.67.88M12 2a10 10 0 0 1 10 10 10 10 0 0 1-10 10c-1.97 0-3.8-.57-5.35-1.55L2 22l1.55-4.65A9.969 9.969 0 0 1 2 12 10 10 0 0 1 12 2m0 2a8 8 0 0 0-8 8c0 1.72.54 3.31 1.46 4.61L4.5 19.5l2.89-.96A7.95 7.95 0 0 0 12 20a8 8 0 0 0 8-8 8 8 0 0 0-8-8z"></path>
                                </svg>
                            </a>
                        </p>
                        @endif
                    </li>
                </ul>

            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-8 col-md-4 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">SCHEDULE PROPERTY</h4>
            </div>

            <div class="card-body mt-3">

                <form action="{{ route('staff.schedule_visit.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" value="{{ $visiterInfo->id }}">
                    <div class="form-group form-float">
                        <label class="form-label">Properties</label>
                        <div class="form-line">
                            <select name="properties" id="property-select" class="form-control  show-tick">
                                <option value="">Select Properties</option>
                                <option value="{{ $visiterInfo->property->unique_id }}" selected>{{ $visiterInfo->property->unique_id }} - {{ $visiterInfo->property->title }}</option>
                            </select>
                        </div>
                        @error('properties')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group form-float mb-2">
                        <label class="form-label">Owner</label>
                        <div class="form-line">
                            <select id="owner-select" name="owner" class="form-control  show-tick">
                                <option value="{{ $visiterInfo->property->owner->id ?? 'N/A' }}" selected>{{ $visiterInfo->property->owner->name ??'N/A' }}</option>
                            </select>
                        </div>
                        @error('owner')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group form-float mb-2">
                        <label class="form-label">Field Manager</label>
                        <div class="form-line">
                            <select name="field_manager_id" class="form-control">
                                @if(isset($visiterInfo->conform_timing->field_manager_id))
                                <option value="{{ $visiterInfo->conform_timing->field_manager_id }}" selected>
                                    {{ $visiterInfo->conform_timing->field_manager->name }}
                                </option>
                                @else
                                <option value="">Select Field Manager</option>
                                @endif
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
                                @if(isset($visiterInfo->users))
                                <option value="{{ $visiterInfo->users->id }}" selected>{{ $visiterInfo->users->name }}</option>
                                @endif
                            </select>
                        </div>

                        @error('users')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group form-float mb-2">
                        <label class="form-label">Select Date and Time</label>
                        @if ($visiterInfo->conform_timing && $visiterInfo->conform_timing->conform_timing == 1)
                        <div class="form-line">
                            <input type="text" class="form-control datetimepicker" name="timing"
                                value="{{ $visiterInfo->conform_timing->timing }}" readonly>
                        </div>
                        @else
                        <input type="hidden" class="form-control datetimepicker mt-2" name="timing">
                        <p>No timing available.</p>
                        @endif

                        @error('timing')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                    <div class="form-group">
                        <button class="btn btn-danger btn-lg waves-effect d-flex align-items-center justify-content-center">
                            <span>Scheduled Visit</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">Field Manager</h4>
            </div>

            <div class="card-body mt-3">
                <form action="{{ route('staff.schedule_properties.update_field_manager') }}" method="POST">

                    @csrf
                    <input type="hidden" name="property_id" value="{{ $visiterInfo->property->unique_id }}">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <strong>Name:</strong>
                            <select name="field_manager" id="field_manager" class="form-control select2">
                                <option value="">Select one</option>
                                @foreach($fieldManager as $field_manager)
                                <option value="{{ $field_manager->id }}"
                                    {{ $visiterInfo->conform_timing && $visiterInfo->conform_timing->field_manager_id == $field_manager->id ? 'selected' : '' }}>
                                    {{ $field_manager->name }}
                                </option>
                                @endforeach
                            </select>
                        </li>
                        <li class="list-group-item">
                            <strong>Email:</strong>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Email"
                                value="{{ old('email', $visiterInfo->conform_timing->field_manager->email ?? '') }}" disabled>
                        </li>
                        <li class="list-group-item">
                            <strong>Mobile:</strong>
                            <!--<input type="text" name="mobile" id="mobile" class="form-control" placeholder="Enter Mobile Number"-->
                            <!--    value="{{ old('mobile', $visiterInfo->conform_timing->field_manager->mobile_no ?? '') }}" disabled>-->
                            <button type="button" id="makeCallField" class="btn btn-primary" data-bs-toggle="modal">
                                Call Now
                            </button>
                            <input type="hidden" class="form-control" id="mobile" name="customer_number" value="{{ old('mobile', $visiterInfo->conform_timing->field_manager->mobile_no ?? '') }}" required>
                            <input type="hidden" class="form-control" id="agentNumberField" name="agent_number" value="{{ auth('staff')->user()->mobile_no }}" required>

                        </li>

                        @if(!empty($visiterInfo->conform_timing) && $visiterInfo->conform_timing->field_manager_id != '')
                        <li class="list-group-item">
                            @if (!empty($visiterInfo->conform_timing) && $visiterInfo->conform_timing->conform_timing == 1)
                            <p><strong>Timing:</strong>
                                <span id="timing_display" class="right">{{ $formattedStart }} - {{ $formattedEnd }}</span>
                            </p>
                            <input type="hidden" id="conform_timing" name="conform_timing" value="{{ $visiterInfo->conform_timing->id }}">
                            @else
                            <p class="list-group-item">
                                <strong>Manual Confirm Timing:</strong>
                                <span class="right">
                                    <a href="https://admin.realtrust.in/conform-timing/field_manager/{{ $visiterInfo->property->unique_id ?? '' }}" target="_blank" rel="noopener noreferrer">
                                        Click Here
                                    </a>
                                </span>
                            </p>

                            <p><strong>Timing:</strong>
                                <a href="{{ route('staff.field_manager.send-time-confirmation', [$visiterInfo->conform_timing->field_manager_id, $visiterInfo->property->unique_id]) }}" class="btn btn-success waves-effect">
                                    <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium css-9gwpq2" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="WhatsAppIcon" style="user-select: none;width: 24px;display: block;flex-shrink: 0;fill: currentcolor;font-size: 210px;color: rgb(255, 255, 255);">
                                        <path d="M16.75 13.96c.25.13.41.2.46.3.06.11.04.61-.21 1.18-.2.56-1.24 1.1-1.7 1.12-.46.02-.47.36-2.96-.73-2.49-1.09-3.99-3.75-4.11-3.92-.12-.17-.96-1.38-.92-2.61.05-1.22.69-1.8.95-2.04.24-.26.51-.29.68-.26h.47c.15 0 .36-.06.55.45l.69 1.87c.06.13.1.28.01.44l-.27.41-.39.42c-.12.12-.26.25-.12.5.12.26.62 1.09 1.32 1.78.91.88 1.71 1.17 1.95 1.3.24.14.39.12.54-.04l.81-.94c.19-.25.35-.19.58-.11l1.67.88M12 2a10 10 0 0 1 10 10 10 10 0 0 1-10 10c-1.97 0-3.8-.57-5.35-1.55L2 22l1.55-4.65A9.969 9.969 0 0 1 2 12 10 10 0 0 1 12 2m0 2a8 8 0 0 0-8 8c0 1.72.54 3.31 1.46 4.61L4.5 19.5l2.89-.96A7.95 7.95 0 0 0 12 20a8 8 0 0 0 8-8 8 8 0 0 0-8-8z"></path>
                                    </svg>
                                </a>
                            </p>
                            @endif
                        </li>
                        @else
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <!-- @if($visiterInfo->gate_pass)
                        @else
                        <p class="text-warning mt-2">Confirm Owner Timming first to select Field Manager</p>
                        @endif -->
                        @endif

                    </ul>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<!--to make call -->
<script>
    $(document).ready(function() {
        $("#makeCall").on("click", function(e) {
            e.preventDefault();

            let customerNumber = $("#customerNumber").val().trim();
            let staffNumber = $("#agentNumber").val().trim();

            $.ajax({
                url: "{{ route('staff.make.call') }}",
                type: "POST",
                data: {
                    customer_number: customerNumber,
                    staff_number: staffNumber
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                success: function(response) {
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: response.message || "Operation completed successfully.",
                        timer: 2000,
                        showConfirmButton: false
                    });

                },
                error: function(xhr) {
                    let errorMessage = "Something went wrong. Please try again!";

                    if (xhr.responseJSON) {
                        if (typeof xhr.responseJSON.error === "string") {
                            errorMessage = xhr.responseJSON.error; // Direct string message
                        } else if (typeof xhr.responseJSON.error === "object") {
                            // Convert object errors to a readable string
                            errorMessage = Object.values(xhr.responseJSON.error).join("\n");
                        }
                    }

                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: errorMessage
                    });
                }
            });
        });
        $("#makeCallField").on("click", function(e) {
            e.preventDefault();

            let customerNumber = $("#mobile").val().trim();
            let staffNumber = $("#agentNumberField").val().trim();
            if (!customerNumber) {
                Swal.fire({
                    icon: "warning",
                    title: "Warning!",
                    text: "Select Field Manager First",
                    timer: 2000,
                    showConfirmButton: false
                });
                return; // Stop execution
            }

            $.ajax({
                url: "{{ route('staff.make.call') }}",
                type: "POST",
                data: {
                    customer_number: customerNumber,
                    staff_number: staffNumber
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                success: function(response) {
                    Swal.fire({
                        icon: "success",
                        title: "Success!",
                        text: response.message || "Operation completed successfully.",
                        timer: 2000,
                        showConfirmButton: false
                    });

                },
                error: function(xhr) {
                    let errorMessage = "Something went wrong. Please try again!";

                    if (xhr.responseJSON) {
                        if (typeof xhr.responseJSON.error === "string") {
                            errorMessage = xhr.responseJSON.error; // Direct string message
                        } else if (typeof xhr.responseJSON.error === "object") {
                            // Convert object errors to a readable string
                            errorMessage = Object.values(xhr.responseJSON.error).join("\n");
                        }
                    }

                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: errorMessage
                    });
                }


            });
        });
    });
</script>
<script>
    $(document).ready(function() {

        // $('.datetimepicker').datetimepicker({
        //     format: 'Y-m-d H:i', // Format for date and time
        //     step: 30, // Interval for time picker in minutes
        //     minDate: 0, // Disallow past dates
        //     autoclose: true,
        //     todayHighlight: true
        // });


        // Event listener for field manager selection change
        $('#field_manager').change(function() {
            var fieldManagerId = $(this).val();
            var properties_id = '{{ $visiterInfo->property->unique_id }}';
            // Make AJAX call to fetch field manager details
            $.ajax({
                url: '{{ route("staff.schedule_properties.get-field-manager") }}', // Adjust the route as needed
                method: 'GET',
                data: {
                    id: fieldManagerId,
                    properties_id: properties_id
                },
                success: function(response) {
                    // Update email and mobile number fields
                    $('#email').val(response.email);
                    $('#mobile').val(response.mobile_no);
                    $('#makeCallField').removeClass('d-none');
                    // Update timing display
                    if (response.timing) {
                        $('#timing_display').text(response.timing);
                        $('#conform_timing').val(response.timing);
                    } else {
                        $('#timing_display').text('No timing available');
                        $('#conform_timing').val('');
                    }
                }
            });
        });
    });
</script>
@endpush