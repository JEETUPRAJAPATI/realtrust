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
        <div class="col-lg-8">
            <div class="row">
                <div class="col-xxl-4 col-xl-12">
                    <div class="card recent-sales overflow-auto">

                        <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                            <h4 class="text-black mb-0 card-title">Weekly Page Title Counts</h4>
                        </div>
                        <div class="card-body">
                            <div style="max-height: 400px; overflow-y: auto;" class="table-responsive">
                                <table class="table table-hover dashboard-task-infos">
                                    <thead>
                                        <tr>
                                            <th>Page Title</th>
                                            <th>Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pageTitleCounts as $title => $count)
                                        <tr>
                                            <td>{{ $title }}</td>
                                            <td>{{ $count }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-xl-12">
                    <div class="card info-card customers-card">
                        <div class="card-body">
                            <div id="activeUsersByCountryChart" style="min-height: 400px; margin-top: 50px;" class="echart"></div>
                        </div>
                    </div>

                </div>
                <!-- Sales Card -->
                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card sales-card">
                        <div class="card-body">
                            <h5 class="card-title">TOTAL PROPERTY</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-cart"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{ $propertycount }}</h6>
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
                            <h5 class="card-title">TOTAL POST</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-currency-dollar"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{ $postcount }}</h6>
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
                            <h5 class="card-title">TOTAL STAFF</h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{ $staffcount }}</h6>
                                    <span class="text-danger small pt-1 fw-bold">12%</span> <span class="text-muted small pt-2 ps-1">decrease</span>
                                </div>
                            </div>

                        </div>
                    </div>

                </div><!-- End Customers Card -->

                <!-- Reports -->

                <!-- End Reports -->
                <!-- Recent Sales -->
                <div class="col-12">
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
                <div class="col-12">
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
                                            <th>Name</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $key => $user)
                                        <tr>
                                            <td>{{ ++$key }}.</td>
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
        </div><!-- End Left side columns -->

        <!-- Right side columns -->
        <div class="col-lg-4">
            <!-- Recent Activity -->



            <!-- Website Traffic -->
            <div class="card">
                <div class="card-body pb-0">
                    <h5 class="card-title">Website Traffic <span>| Today</span></h5>

                    <div id="trafficChart" style="min-height: 400px;" class="echart"></div>

                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            // Data from the controller
                            var totalUsers = @json($totalUsers);
                            var totalPageViews = @json($totalPageViews);
                            var countriesData = @json($countriesData);
                            var newUsers = @json($newUsers);

                            // Prepare pie chart data
                            var pieChartData = [{
                                    value: totalUsers,
                                    name: 'Users'
                                },
                                {
                                    value: totalPageViews,
                                    name: 'Page Views'
                                },
                                {
                                    value: newUsers,
                                    name: 'New Users'
                                }
                            ];

                            // Render the pie chart
                            echarts.init(document.querySelector("#trafficChart")).setOption({
                                tooltip: {
                                    trigger: 'item',
                                    formatter: '{b}: {c} ({d}%)'
                                },
                                legend: {
                                    top: '5%',
                                    left: 'center'
                                },
                                series: [{
                                    name: 'Traffic Data',
                                    type: 'pie',
                                    radius: ['40%', '70%'],
                                    avoidLabelOverlap: true,
                                    label: {
                                        show: true,
                                        formatter: '{b}\n{c}',
                                        fontSize: 12
                                    },
                                    emphasis: {
                                        label: {
                                            show: true,
                                            fontSize: '14',
                                            fontWeight: 'bold'
                                        }
                                    },
                                    labelLine: {
                                        show: true
                                    },
                                    data: pieChartData
                                }]
                            });



                            // Prepare country-wise active users chart data
                            var countryNames = countriesData.map(item => item.country);
                            var activeUsers = countriesData.map(item => item.activeUsers);

                            // Render the active users by country bar chart
                            echarts.init(document.querySelector("#activeUsersByCountryChart")).setOption({
                                title: {
                                    text: 'Active Users by Country',
                                    left: 'center',
                                    textStyle: {
                                        fontSize: 16,
                                        fontWeight: 'bold'
                                    }
                                },
                                tooltip: {
                                    trigger: 'axis',
                                    axisPointer: {
                                        type: 'shadow' // Cursor shows shadow on hover
                                    },
                                    formatter: '{b}: {c} Active Users'
                                },
                                grid: {
                                    left: '3%',
                                    right: '4%',
                                    bottom: '3%',
                                    containLabel: true
                                },
                                xAxis: {
                                    type: 'category',
                                    data: countryNames,
                                    axisLabel: {
                                        rotate: 30, // Rotate labels for better readability
                                        fontSize: 10
                                    }
                                },
                                yAxis: {
                                    type: 'value',
                                    name: 'Active Users'
                                },
                                series: [{
                                    name: 'Active Users',
                                    type: 'bar',
                                    data: activeUsers,
                                    color: '#5470C6', // Blue
                                    barWidth: '50%', // Adjust bar width for better readability
                                }]
                            });
                        });
                    </script>


                </div>
            </div><!-- End Website Traffic -->

            <!-- News & Updates Traffic -->
            <div class="card">

                <div class="card-body pb-0">
                    <h5 class="card-title">News &amp; Updates</h5>
                    <div class="news">
                        @foreach($posts as $key => $post)
                        <div class="post-item clearfix">

                            @if(Storage::disk('public')->exists('posts/'.$post->image))
                            <img src="{{Storage::url('posts/'.$post->image)}}" alt="{{$post->title}}" class="img-responsive img-rounded" width="50%">
                            @endif
                            <h4><a href="#"> <span title="{{ $post->title }}">
                                        {{ Str::limit($post->title, 30) }}
                                    </span></a></h4>
                            <p>
                                <span title="{{ $post->body }}">
                                {!! Str::limit($post->body, 80) !!}
                                </span>
                            </p>
                        </div>
                        @endforeach
                    </div><!-- End sidebar recent posts-->

                </div>
            </div><!-- End News & Updates -->
        </div><!-- End Right side columns -->
    </div>
</section>

@endsection

@push('scripts')



@endpush