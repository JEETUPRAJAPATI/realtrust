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
                            <h5 class="card-title">TOTAL OWNER</h5>

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

                <!-- Revenue Card -->
                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card revenue-card">
                        <div class="card-body">
                            <h5 class="card-title">TOTAL FIELD MANAGER</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{ $fieldManagerCount }}</h6>
                                    <span class="text-success small pt-1 fw-bold">8%</span> <span class="text-muted small pt-2 ps-1">increase</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- End Revenue Card -->

                <!-- Customers Card -->
                <div class="col-xxl-4 col-xl-12">

                    <div class="card info-card customers-card">

                        <div class="card-body">
                            <h5 class="card-title">TOTAL USERS </h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{ $usercount }}</h6>
                                    <span class="text-danger small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">decrease</span>

                                </div>
                            </div>

                        </div>
                    </div>

                </div><!-- End Customers Card -->
            </div>
        </div><!-- End Left side columns -->
        <div class="col-sm-12 d-flex gap-3">
            <!-- Recent Sales -->
            <div class="col-6">
                <div class="card recent-sales overflow-auto">
                    <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                        <h4 class="text-black mb-0 card-title">RECENT PROPERTIES</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover dashboard-task-infos">
                                <thead>
                                    <tr>
                                        <th>SL.</th>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Price</th>
                                        <th>City</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($properties as $key => $property)
                                    <tr>
                                        <td>{{ ++$key }}.</td>
                                        <td>

                                            @if(Storage::disk('public')->exists('property/' . $property->owner_id . '/' . $property->unique_id .'/'. $property->image))
                                            <img style="height: 82px;width: 94px;" src="{{ Storage::url('property/' . $property->owner_id . '/' . $property->unique_id .'/'. $property->image) }}" alt="{{ $property->title }}" class="img-responsive img-rounded"><br>
                                            @endif
                                        </td>
                                        <td>
                                            <span title="{{ $property->title }}">
                                                {{ Str::limit($property->title, 30) }}
                                            </span>
                                        </td>
                                        <td>&#8377;{{ $property->price }}</td>
                                        <td>{{ $property->city }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- End Recent Sales -->

            <!-- Top Selling -->

            <!-- End Top Selling -->
            <div class="col-6">
                <div class="card">

                    <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                        <h4 class="text-black mb-0 card-title">USER LIST</h4>
                    </div>
                    <div class="card-body">
                        </thead>
                        <div class="table-responsive">
                            <table class="table table-hover dashboard-task-infos">
                                <thead>
                                    <tr>
                                        <th>SL.</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $key => $user)
                                    <tr>
                                        <td>{{ ++$key }}.</td>
                                        <td>
                                            @if(Storage::disk('public')->exists('users/'.$user->image))
                                            <img src="{{Storage::url('users/'.$user->image)}}" alt="{{$user->title}}" style="height: 82px;width: 94px;" class="img-responsive img-rounded">
                                            @endif
                                        </td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('backend/plugins/jquery-countto/jquery.countTo.js') }}"></script>
<script src="{{ asset('backend/js/pages/index.js') }}"></script>
@endpush