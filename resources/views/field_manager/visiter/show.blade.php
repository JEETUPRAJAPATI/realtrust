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
                    {{$scheduleVisit->property->title}}

                </p>

                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Price:</strong>
                        <span class="right">{{$scheduleVisit->property->price}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Bedroom:</strong>
                        <span class="right">{{$scheduleVisit->property->bedroom}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Bathroom:</strong>
                        <span class="right">{{$scheduleVisit->property->bathroom}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>City:</strong>
                        <span class="right">{{$scheduleVisit->property->city}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Locality:</strong>
                        <span class="right">{{$scheduleVisit->property->locality}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Society name :</strong>
                        <span class="right">{{$scheduleVisit->property->society_name }}</span>
                    </li>
                </ul>
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Type:</strong>
                        <span class="right">{{$scheduleVisit->property->type}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Bhk:</strong>
                        <span class="right">{{$scheduleVisit->property->bhk}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Area:</strong>
                        <span class="right">{{$scheduleVisit->property->area}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Furnish Type:</strong>
                        <span class="right">{{$scheduleVisit->property->furnish_type}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Available For:</strong>
                        <span class="right">{{$scheduleVisit->property->available_for}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Tenant Type:</strong>
                        <span class="right">{{$scheduleVisit->property->tenant_type}}</span>
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
                        <span class="right">{{$scheduleVisit->user->name}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Email:</strong>
                        <span class="right">{{$scheduleVisit->user->email}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Mobile:</strong>
                        <span class="right">{{$scheduleVisit->user->mobile_no}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Otp varification:</strong>
                        <span class="right"> @if ($scheduleVisit->otp_verification === 'pending')
                            <span class="badge bg-red">Pending</span>
                            @elseif ($scheduleVisit->otp_verification === 'done')
                            <span class="badge bg-green">Done</span>
                            @else
                            <span class="badge bg-secondary">Unknown</span>
                            @endif</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Timing:</strong>
                        <span class="right">{{Carbon\Carbon::parse($scheduleVisit->timing)->format('l, F j, Y \a\t g:i A')}}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="body">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">Owner</h4>
            </div>
            <div class="card-body">
                <ul class="list-group">

                    <li class="list-group-item">
                        <strong>Otp varification:</strong>

                        @if ($scheduleVisit->otp_verification === 'pending')
                        <span class="right badge bg-red">Pending</span>
                        @elseif ($scheduleVisit->otp_verification === 'done')
                        <span class="badge bg-green">Done</span>
                        @else
                        <span class="badge bg-secondary">Unknown</span>
                        @endif
                    </li>
                    <li class="list-group-item">
                        <strong>Timing:</strong>
                        <span class="right">{{Carbon\Carbon::parse($scheduleVisit->timing)->format('l, F j, Y \a\t g:i A')}}</span>
                    </li>
                </ul>
                <a href="{{ route('field_manager.visiter.index') }}" class="btn btn-danger btn-lg waves-effect mt-2">
                <i class="bi bi-arrow-left"></i>
                <span>BACK</span>
                </a>
                <a href="{{ route('field_manager.visiter.edit', $scheduleVisit->id) }}"
                    class="btn bg-green btn-lg waves-effect waves-float {{ $scheduleVisit->otp_verification === 'done' ? 'disabled' : '' }}  mt-2"
                    data-toggle="tooltip"
                    data-placement="top"
                    title="{{ $scheduleVisit->otp_verification === 'done' ? 'Verification Completed' : 'OTP Verification' }}"
                    data-original-title="OTP Verification">
                    <i class="bi bi-key"></i>
                </a>
            </div>
        </div>
    </div>

</div>

@endsection


@push('scripts')

@endpush