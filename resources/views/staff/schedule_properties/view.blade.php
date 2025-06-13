@extends('backend.layouts.app')

@section('title', 'Show Property')

@push('styles')

@endpush
@section('content')

<div class="block-header"></div>
<div class="row clearfix">
    <!-- Property Details Section -->
    <div class="col-lg-8 col-md-4 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">SHOW PROPERTY</h4>
            </div>
            <div class="card-body">
                <p>
                    {{$visiterInfo->property->title}}
                    <br>
                    <small>Posted By <strong>{{$visiterInfo->property->owner->name}}</strong> on {{$visiterInfo->property->created_at->toFormattedDateString()}}</small>
                </p>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Price:</strong>
                        <span class="float-end">{{$visiterInfo->property->price}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Bedroom:</strong>
                        <span class="float-end">{{$visiterInfo->property->bedroom}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Bathroom:</strong>
                        <span class="float-end">{{$visiterInfo->property->bathroom}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>City:</strong>
                        <span class="float-end">{{$visiterInfo->property->city}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Locality:</strong>
                        <span class="float-end">{{$visiterInfo->property->locality}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Society name:</strong>
                        <span class="float-end">{{$visiterInfo->property->society_name }}</span>
                    </li>
                </ul>
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Type:</strong>
                        <span class="float-end">{{$visiterInfo->property->type}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Bhk:</strong>
                        <span class="float-end">{{$visiterInfo->property->bhk}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Area:</strong>
                        <span class="float-end">{{$visiterInfo->property->area}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Furnish Type:</strong>
                        <span class="float-end">{{$visiterInfo->property->furnish_type}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Available For:</strong>
                        <span class="float-end">{{$visiterInfo->property->available_for}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Tenant Type:</strong>
                        <span class="float-end">{{$visiterInfo->property->tenant_type}}</span>
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
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Name:</strong>
                        <span class="float-end">{{$visiterInfo->users->name}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Email:</strong>
                        <span class="float-end">{{$visiterInfo->users->email}}</span>
                    </li>
                    <!--<li class="list-group-item">-->
                    <!--    <strong>Mobile:</strong>-->
                    <!--    <span class="float-end">{{$visiterInfo->users->mobile_no}}</span>-->
                    <!--</li>-->
                    <li class="list-group-item">
                        <strong>Otp verification:</strong>
                        <span class="float-end">
                            @if (!empty($visiterInfo->schedule_visit_date) && isset($visiterInfo->schedule_visit_date->otp_verification))
                            @if ($visiterInfo->schedule_visit_date->otp_verification === 'pending')
                            <span class="badge bg-danger">Pending</span>
                            @elseif ($visiterInfo->schedule_visit_date->otp_verification === 'done')
                            <span class="badge bg-success">Done</span>
                            @else
                            <span class="badge bg-secondary">Unknown</span>
                            @endif
                            @else
                            <span class="badge bg-secondary">No Data Available</span>
                            @endif
                        </span>
                    </li>

                    <li class="list-group-item">
                        <strong>Timing:</strong>
                        <span class="float-end">
                            @if (!empty($visiterInfo->schedule_visit_date) && isset($visiterInfo->schedule_visit_date->timing))
    
                                    @php
                                        // Split the timing string into start and end times
                                        [$startDatetime, $endDatetime] = explode(' - ', $visiterInfo->schedule_visit_date->timing);

                                        // Format the start and end times using Carbon
                                        $formattedStart = Carbon\Carbon::createFromFormat('m/d/Y h:i A', $startDatetime)->format('l, F j, Y \a\t g:i A');
                                        $formattedEnd = Carbon\Carbon::createFromFormat('m/d/Y h:i A', $endDatetime)->format('l, F j, Y \a\t g:i A');
                                    @endphp
                                    {{ $formattedStart }} - {{ $formattedEnd }}
                            @else
                            <span class="text-muted">No Timing Available</span>
                            @endif
                        </span>
                    </li>

                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">Field Manager</h4>
            </div>
            <div class="card-body">
                @if (!empty($visiterInfo->schedule_visit_date) && !empty($visiterInfo->schedule_visit_date->field_manager))
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Name:</strong>
                        <span class="float-end">
                            {{ $visiterInfo->schedule_visit_date->field_manager->name ?? 'No Data Available' }}
                        </span>
                    </li>
                    <li class="list-group-item">
                        <strong>Email:</strong>
                        <span class="float-end">
                            {{ $visiterInfo->schedule_visit_date->field_manager->email ?? 'No Data Available' }}
                        </span>
                    </li>
                    <li class="list-group-item">
                        <strong>Mobile:</strong>
                        <!--<span class="float-end">-->
                        <!--    {{ $visiterInfo->schedule_visit_date->field_manager->mobile_no ?? 'No Data Available' }}-->
                        <!--</span>-->
                        <button  type="button" id="makeCallField" class="btn btn-primary " data-bs-toggle="modal">
                            Call Now
                         </button>
                        <input type="hidden" class="form-control" id="mobile"  name="customer_number" value="{{ old('mobile', $visiterInfo->conform_timing->field_manager->mobile_no ?? '') }}" required>
                        <input type="hidden" class="form-control" id="agentNumberField" name="agent_number" value="{{ auth('staff')->user()->mobile_no }}" required>
                    </li>
                </ul>
                @else
                <p class="text-center text-muted mb-0">Field Manager details are not available.</p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">Owner</h4>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Name:</strong>
                        <span class="float-end">{{$visiterInfo->property->owner->name}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Email:</strong>
                        <span class="float-end">{{$visiterInfo->property->owner->email}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Mobile:</strong>
                        <button type="button" id="makeCall" class="btn btn-primary" data-bs-toggle="modal">
                            Call Now
                         </button>
                        <input type="hidden" class="form-control" id="customerNumber"  name="customer_number" value="{{ $visiterInfo->property->owner->mobile_no }}" required>
                        <input type="hidden" class="form-control" id="agentNumber" name="agent_number" value="{{ auth('staff')->user()->mobile_no }}" required>
                    </li>
                    <li class="list-group-item">
                        <strong>Otp verification:</strong>
                        <span class="float-end">
                            @if (!empty($visiterInfo->schedule_visit_date) && $visiterInfo->schedule_visit_date->otp_verification)
                            @if ($visiterInfo->schedule_visit_date->otp_verification === 'pending')
                            <span class="badge bg-danger">Pending</span>
                            @elseif ($visiterInfo->schedule_visit_date->otp_verification === 'done')
                            <span class="badge bg-success">Done</span>
                            @else
                            <span class="badge bg-secondary">Unknown</span>
                            @endif
                            @else
                            <span class="badge bg-secondary">No Data Available</span>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <strong>Timing:</strong>
                        <span class="float-end">
                            @if (!empty($visiterInfo->schedule_visit_date) && $visiterInfo->schedule_visit_date->timing)
                            @php
                                        // Split the timing string into start and end times
                                        [$startDatetime, $endDatetime] = explode(' - ', $visiterInfo->schedule_visit_date->timing);

                                        // Format the start and end times using Carbon
                                        $formattedStart = Carbon\Carbon::createFromFormat('m/d/Y h:i A', $startDatetime)->format('l, F j, Y \a\t g:i A');
                                        $formattedEnd = Carbon\Carbon::createFromFormat('m/d/Y h:i A', $endDatetime)->format('l, F j, Y \a\t g:i A');
                                    @endphp
                                    {{ $formattedStart }} - {{ $formattedEnd }}
                            @else
                            No Data Available
                            @endif
                        </span>
                    </li>
                </ul>
                <a href="{{ route('staff.schedule_properties.index') }}" class="btn btn-light mt-2">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
    $(document).ready(function () {
        $("#makeCall").on("click", function (e) {
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
                success: function (response) {
                               Swal.fire({
                icon: "success",
                title: "Success!",
                text: response.message || "Operation completed successfully.",
                timer: 2000,
                showConfirmButton: false
            });

                },
                error: function (xhr) {
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
        $("#makeCallField").on("click", function (e) {
            e.preventDefault();
            
            let customerNumber = $("#mobile").val().trim();
            let staffNumber = $("#agentNumberField").val().trim();

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
                success: function (response) {
                                Swal.fire({
                icon: "success",
                title: "Success!",
                text: response.message || "Operation completed successfully.",
                timer: 2000,
                showConfirmButton: false
            });

                },
                error: function (xhr) {
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
@endpush