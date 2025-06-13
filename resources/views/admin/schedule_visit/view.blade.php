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
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">SHOW PROPERT</h4>
                <a href="{{ route('admin.schedule_visit.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>

            <div class="header">
                <h2>
                    {{$visiterInfo->property->title}}
                    <br>
                    <small>Posted By <strong>{{$visiterInfo->property->owner->name}}</strong> on {{$visiterInfo->property->created_at->toFormattedDateString()}}</small>
                </h2>
            </div>


            <!-- Card Body -->
            <div class="card-body mt-3">
                <div class="header">
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
                            <span class="right">{{$visiterInfo->property->city}}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Locality:</strong>
                            <span class="right">{{$visiterInfo->property->locality}}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Society name :</strong>
                            <span class="right">{{$visiterInfo->property->society_name }}</span>
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
                        <li class="list-group-item">
                            <strong>Tenant Type:</strong>
                            <span class="right">{{$visiterInfo->property->tenant_type}}</span>
                        </li>
                    </ul>
                </div>
            </div>


        </div>


        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">

            <div class="card">
                <div class="header">
                    <h2>Field Manager</h2>
                </div>
                <div class="body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <strong>Name:</strong>
                            <span class="right">{{$visiterInfo->field_manager->name}}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Email:</strong>
                            <span class="right">{{$visiterInfo->field_manager->email}}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Mobile:</strong>
                            <span class="right">{{$visiterInfo->field_manager->mobile_no}}</span>
                        </li>


                        <li class="list-group-item">
                            <strong>Timing:</strong>
                            @if(isset($visiterInfo->conform_timing) && $visiterInfo->conform_timing->timing)
                            <span class="right">
                                {{Carbon\Carbon::parse($visiterInfo->conform_timing->timing)->format('l, F j, Y \a\t g:i A')}}
                            </span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card">
                <div class="header">
                    <h2>Owner</h2>
                </div>
                <div class="body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <strong>Name:</strong>
                            <span class="right">{{$visiterInfo->owner->name}}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Email:</strong>
                            <span class="right">{{$visiterInfo->owner->email}}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Mobile:</strong>
                            <span class="right">{{$visiterInfo->owner->mobile_no}}</span>
                        </li>

                        <li class="list-group-item">
                            <strong>Timing:</strong>
                            @if(isset($visiterInfo->conform_timing) && $visiterInfo->conform_timing->timing)
                            <span class="right">
                                {{ Carbon\Carbon::parse($visiterInfo->conform_timing->timing)->format('l, F j, Y \a\t g:i A') }}
                            </span>
                            @endif
                        </li>

                    </ul>
                    <a href="{{ route('admin.schedule_visit.index') }}" class="btn btn-light">
                        <i class="bi bi-arrow-left-short"></i> BACK
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row clearfix">
        <!-- Property Details Section -->
        <div class="col-lg-8 col-md-4 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>User List</h2>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Otp Verification</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($visiterInfo->userLists as $userList)
                                <tr>
                                    <td>{{ $userList->user->name }}</td>
                                    <td>{{ $userList->user->email }}</td>
                                    <td>{{ $userList->user->mobile_no }}</td>
                                    <td>
                                        @if ($userList->otp_verification === 'pending')
                                        <span class="badge bg-red">Pending</span>
                                        @elseif ($userList->otp_verification === 'done')
                                        <span class="badge bg-green">Done</span>
                                        @else
                                        <span class="badge bg-secondary">Unknown</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection


    @push('scripts')

    @endpush