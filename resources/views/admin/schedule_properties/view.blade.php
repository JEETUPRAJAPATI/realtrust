@extends('backend.layouts.app')

@section('title', 'Show Property')

@push('styles')
@endpush
@section('content')
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
                    <li class="list-group-item">
                        <strong>Mobile:</strong>
                        <span class="float-end">{{$visiterInfo->users->mobile_no}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Otp verification:</strong>
                        <span class="float-end">
                            @if ($visiterInfo->schedule_visit_date->otp_verification === 'pending')
                            <span class="badge bg-danger">Pending</span>
                            @elseif ($visiterInfo->schedule_visit_date->otp_verification === 'done')
                            <span class="badge bg-success">Done</span>
                            @else
                            <span class="badge bg-secondary">Unknown</span>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <strong>Timing:</strong>
                        <span class="float-end">{{Carbon\Carbon::parse($visiterInfo->timing)->format('l, F j, Y \a\t g:i A')}}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">Field Manager</h4>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Name:</strong>
                        <span class="float-end">{{$visiterInfo->schedule_visit_date->field_manager->name}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Email:</strong>
                        <span class="float-end">{{$visiterInfo->schedule_visit_date->field_manager->email}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Mobile:</strong>
                        <span class="float-end">{{$visiterInfo->schedule_visit_date->field_manager->mobile_no}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Otp verification:</strong>
                        <span class="float-end">
                            @if ($visiterInfo->schedule_visit_date->otp_verification === 'pending')
                            <span class="badge bg-danger">Pending</span>
                            @elseif ($visiterInfo->schedule_visit_date->otp_verification === 'done')
                            <span class="badge bg-success">Done</span>
                            @else
                            <span class="badge bg-secondary">Unknown</span>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <strong>Timing:</strong>
                        <span class="float-end">{{Carbon\Carbon::parse($visiterInfo->schedule_visit_date->timing)->format('l, F j, Y \a\t g:i A')}}</span>
                    </li>
                </ul>
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
                        <span class="float-end">{{$visiterInfo->property->owner->mobile_no}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Otp verification:</strong>
                        <span class="float-end">
                            @if ($visiterInfo->schedule_visit_date->otp_verification === 'pending')
                            <span class="badge bg-danger">Pending</span>
                            @elseif ($visiterInfo->schedule_visit_date->otp_verification === 'done')
                            <span class="badge bg-success">Done</span>
                            @else
                            <span class="badge bg-secondary">Unknown</span>
                            @endif
                        </span>
                    </li>
                    <li class="list-group-item">
                        <strong>Timing:</strong>
                        <span class="float-end">{{Carbon\Carbon::parse($visiterInfo->schedule_visit_date->timing)->format('l, F j, Y \a\t g:i A')}}</span>
                    </li>
                </ul>
                <a href="{{ route('admin.schedule_properties.index') }}" class="btn btn-light mt-2">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
@endpush