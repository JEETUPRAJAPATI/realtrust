@extends('backend.layouts.app')

@section('title', 'Dashboard')

@push('styles')

@endpush


@section('content')


<div class="pagetitle">
    <h1>Dashboard</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.html">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section dashboard">
    <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-12">
            <div class="row">

                <!-- Sales Card -->
                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card sales-card">
                        <div class="card-body">
                            <h5 class="card-title">TOTAL Visiter</h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-cart"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{ $ownercount }}</h6>
                                    <span class="text-success small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">increase</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div><!-- End Sales Card -->
            </div>
        </div><!-- End Left side columns -->
        <div class="col-sm-12 ">
            <!-- Recent Sales -->

            <div class="card recent-sales overflow-auto">
                <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                    <h4 class="text-black mb-0 card-title">Recent Visiter</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover dashboard-task-infos">
                            <thead>
                                <tr>
                                    <th>SL.</th>
                                    <th>Property Name</th>
                                    <!--<th>User</th>-->
                                    <!--<th>Owner</th>-->
                                    <th>Timing</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($field_managers->isEmpty())
                                <tr>
                                    <td colspan="5" class="text-center">No data available</td>
                                </tr>
                                @else
                                @foreach($field_managers as $key => $field_manager)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        @if ($field_manager->property)
                                        {{ $field_manager->property->title . ' - ' . $field_manager->property->unique_id }}
                                        @else
                                        <p>No property assigned</p>
                                        @endif
                                    </td>
                                    <!--<td>-->
                                    <!--    @if ($field_manager->user)-->
                                    <!--    <p><strong>User Name:</strong> {{ $field_manager->user->name }}</p>-->
                                    <!--    <p><strong>Email:</strong> {{ $field_manager->user->email }}</p>-->
                                    <!--    @else-->
                                    <!--    <p>No user assigned</p>-->
                                    <!--    @endif-->
                                    <!--</td>-->
                                    <!--<td>-->
                                    <!--    @if ($field_manager->owner)-->
                                    <!--    <p><strong>Owner Name:</strong> {{ $field_manager->owner->name }}</p>-->
                                    <!--    <p><strong>Email:</strong> {{ $field_manager->owner->email }}</p>-->
                                    <!--    @else-->
                                    <!--    <p>No owner assigned</p>-->
                                    <!--    @endif-->
                                    <!--</td>-->
                                    <td>
                                        @php
                                        // Split the timing string into start and end times
                                        [$startDatetime, $endDatetime] = explode(' - ', $field_manager->timing);

                                        // Format the start and end times using Carbon
                                        $formattedStart = Carbon\Carbon::createFromFormat('m/d/Y h:i A', $startDatetime)->format('l, F j, Y \a\t g:i A');
                                        $formattedEnd = Carbon\Carbon::createFromFormat('m/d/Y h:i A', $endDatetime)->format('l, F j, Y \a\t g:i A');
                                        @endphp
                                        {{ $formattedStart }} - {{ $formattedEnd }}
                                    </td>
                                </tr>
                                @endforeach
                                @endif

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- End Recent Sales -->

            <!-- Top Selling -->

            <!-- End Top Selling -->

        </div>
    </div>
</section>
@endsection
@push('scripts')

<!-- Jquery CountTo Plugin Js -->
<script src="{{ asset('backend/plugins/jquery-countto/jquery.countTo.js') }}"></script>

<!-- Sparkline Chart Plugin Js -->
<script src="{{ asset('backend/js/pages/index.js') }}"></script>

<script>
    function updateLocation(fieldManagerId) {
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(position => {
            const {
                latitude,
                longitude
            } = position.coords;

    fetch(`/field_manager/location/update/${fieldManagerId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({
            latitude,
            longitude
        }),
    });
    });
    } else {
        alert("Geolocation is not supported by this browser.");
    }
    }
    $(document).ready(function() {
            const fieldManagerId = '{{ Auth::guard('field_manager')->user()->id }}';
        updateLocation(fieldManagerId);
        setInterval(() => {
            updateLocation(fieldManagerId);
        }, 120000);
    });


    // function getRandomCoordinates(baseLat, baseLng, range = 0.01) {
    //     // Generate random latitude within the specified range
    //     const latitude = (baseLat + (Math.random() * range * 2 - range)).toFixed(6);
    //     // Generate random longitude within the specified range
    //     const longitude = (baseLng + (Math.random() * range * 2 - range)).toFixed(6);
    //     return {
    //         latitude,
    //         longitude
    //     };
    // }

    // function updateLocation(fieldManagerId) {
    //     let latitude, longitude;
    //     ({
    //         latitude,
    //         longitude
    //     } = getRandomCoordinates(21.226587077, 72.83755502));
    //     updateServerLocation(fieldManagerId, latitude, longitude);
    // }

    // function updateServerLocation(fieldManagerId, latitude, longitude) {
    //     fetch(`/field_manager/location/update/${fieldManagerId}`, {
    //         method: 'POST',
    //         headers: {
    //             'Content-Type': 'application/json',
    //             'X-CSRF-TOKEN': '{{ csrf_token() }}',
    //         },
    //         body: JSON.stringify({
    //             latitude,
    //             longitude
    //         }),
    //     });
    // }

    // $(document).ready(function() {
    //     const fieldManagerId = '{{ Auth::guard('field_manager')->user()->id }}';
    //     updateLocation(fieldManagerId);
    //     setInterval(() => {
    //         updateLocation(fieldManagerId);
    //     }, 2000000);
    // });
</script>
@endpush