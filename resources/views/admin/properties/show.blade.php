@extends('backend.layouts.app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@section('title', 'Show Property')

@push('styles')

<style>
    .img-full-width {
        width: 100%;
        height: auto;
        object-fit: cover;
    }

    .carousel-inner {
        width: 100%;
        max-height: 500px;
    }

    .btn-group,
    .btn-group-vertical {
        box-shadow: none !important;
    }
</style>

@endpush
@section('content')

<div class="block-header"></div>
<div class="row clearfix">
    <!-- Property Details Section -->
    <div class="col-lg-8 col-md-4 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">SHOW PROPERTY</h4>
            </div>
            <h6 class="card-body mt-2 ">
                {{$property->title}}
                <small>Posted By <strong>{{$property->owner->name}}</strong> on {{$property->created_at->toFormattedDateString()}}</small>
            </h6>
            <!-- Card card-Body mt-2 -->
            <div class="card-body mt-2 ">
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Price:</strong>
                        <span class="right">{{$property->price}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Deposit:</strong>
                        <span class="right">{{$property->deposit}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Monthly Rent:</strong>
                        <span class="right">{{$property->monthly_rent}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Maintenance:</strong>
                        <span class="right">{{$property->maintenance}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Bedroom:</strong>
                        <span class="right">{{$property->bedroom}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Bathroom:</strong>
                        <span class="right">{{$property->bathroom}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>City:</strong>
                        <span class="right">{{$city->city_name}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Locality:</strong>
                        <span class="right">{{$localityName}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Society name :</strong>
                        <span class="right">{{$society->name }}</span>
                    </li>
                </ul>
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Type:</strong>
                        <span class="right">{{$property->type}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Bhk:</strong>
                        <span class="right">{{$property->bhk}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Flat Number:</strong>
                        <span class="right">{{$property->flat_no}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Block Number:</strong>
                        <span class="right">{{$property->block_no}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Area:</strong>
                        <span class="right">{{$property->area}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Furnish Type:</strong>
                        <span class="right">{{$property->furnish_type}}</span>
                    </li>

                    <li class="list-group-item">
                        <strong>Available For:</strong>
                        <span class="right">{{$property->available_for}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Tenant Type:</strong>
                        <span class="right">{{$property->tenant_type}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Highlight:</strong>
                        <span class="right">{{$property->highlight_type}}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Status:</strong>
                        <span class="right">
                            <form id="status-form-{{ $property->id }}" action="{{ route('admin.properties.updateStatus', ['id' => $property->id]) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="btn-group">
                                    <button type="button" id="btn-{{ $property->id }}" class="btn btn-{{ strtolower($property->status) }} dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ ucfirst($property->status) }} <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @foreach(['Active', 'Inactive', 'Reject', 'Draft', 'Request', 'Expired', 'Delete'] as $status)
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item" data-property-id="{{ $property->id }}" data-status="{{ $status }}">
                                                {{ $status }}
                                            </a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </form>
                        </span>
                    </li>

                </ul>
            </div>

            <div class="card-body mt-2">
                <h5>Description</h5>
                {!! $property->description !!}
            </div>
        </div>

        <!-- Map Section -->
        <div class="card">
            <div class="header">
                <h2>MAP</h2>
            </div>
            <div class="card-body mt-2">
                <!--<div id="gmap_markers" class="gmap"></div>-->
                    <div style=" overflow: hidden;">
                        {!! $society->embeded_map !!}
                    </div>
            </div>
        </div>

        <!-- Floor Plan Section -->
        @if($property->floor_plan)
        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">FLOOR PLAN</h4>
            </div>
            @if($property->floor_plan && $property->floor_plan != 'default.png')
            <div class="gallery-image-edit">
                <img src="{{ Storage::url('property/' . $property->owner_id . '/' . $property->unique_id .'/'. $property->floor_plan) }}" alt="{{ $property->title }}" class="img-fluid img-thumbnail rounded"><br>

            </div>
            @endif
        </div>
        @endif

        <!-- Gallery Images Section -->
        @if(!$property->gallery->isEmpty())

        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">GALLERY IMAGE</h4>
            </div>
            <div class="card-body mt-2 mt-3">
                <div class="gallery-box" id="gallerybox">
                    @foreach($property->gallery as $gallery)
                    <div class="gallery-image-edit" id="gallery-{{ $gallery->id }}">
                        @php
                            // Check if it's a YouTube URL
                            $isYoutube = filter_var($gallery->name, FILTER_VALIDATE_URL) && 
                                         (strpos($gallery->name, 'youtube.com') !== false || strpos($gallery->name, 'youtu.be') !== false);

                            // Extract YouTube Video ID
                            if ($isYoutube) {
                                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $gallery->name, $matches);
                                $youtubeID = $matches[1] ?? null;
                            }
                        @endphp

                        @if($isYoutube && $youtubeID)
                            <iframe width="100%" height="300" src="https://www.youtube.com/embed/{{ $youtubeID }}" frameborder="0" allowfullscreen></iframe>
                        @else
                            <img class="img-fluid img-thumbnail rounded"
                                src="{{ Storage::url('property/' .$property->owner_id. '/' . $gallery->property->unique_id . '/gallery/' . $gallery->name) }}" 
                                alt="{{ $gallery->name }}">
                        @endif
                    </div>

                    @endforeach
                </div>
                <!-- <div class="gallery-box">
                            <hr>
                        </div> -->
            </div>
        </div>

        @endif

        <!-- Comments Section -->
    </div>

    <!-- Sidebar Section -->
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
        <!-- Property Type Section -->
        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">TYPE</h4>
            </div>
            <div class="card-body mt-2">
                <strong class="label bg-red">{{$property->type}}</strong> for <strong class="label bg-blue">{{$property->purpose}}</strong>
            </div>
        </div>

        <!-- Features Section -->
        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">FEATURES</h4>
            </div>
            <div class="card-body mt-2">
                @foreach($property->features as $feature)
                <span class="badge rounded-pill bg-light text-dark">{{$feature->name}}</span>
                @endforeach
            </div>
        </div>

        <!-- Featured Image Section -->
        <div class="card ">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">FEATURED IMAGE</h4>
            </div>
            <div class="card-body mt-2 ">
                <img src="{{ Storage::url('property/' . $property->owner_id . '/' . $property->unique_id .'/'. $property->image) }}" alt="{{ $property->title }}" class="img-responsive img-rounded gallery-image-edit"><br>
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('admin.properties.index') }}" class="btn btn-default btn-sm waves-effect">
                        <i class="bi bi-arrow-left-short"></i> BACK
                    </a>
                    <!--<a href="{{ route('admin.properties.edit', $property->slug) }}" class="btn btn-info btn-lg waves-effect">-->
                    <!--    <span>EDIT</span>-->
                    <!--</a>-->
                </div>
            </div>

        </div>
    </div>
</div>
@endsection


@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyByNeYE_Ni2ChK0hJtK8aAUm0J_4YwY20M"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    // document.addEventListener('DOMContentLoaded', function() {
    //     const latitude = {
    //         {
    //             $property - > latitude
    //         }
    //     };
    //     const longitude = {
    //         {
    //             $property - > longitude
    //         }
    //     };
    //     const mapOptions = {
    //         center: {
    //             lat: latitude,
    //             lng: longitude
    //         },
    //         zoom: 14,
    //     };

    //     const map = new google.maps.Map(document.getElementById('gmap_markers'), mapOptions);

    //     const marker = new google.maps.Marker({
    //         position: {
    //             lat: latitude,
    //             lng: longitude
    //         },
    //         map: map,
    //         title: 'Location',
    //     });
    // });

    $(document).ready(function() {
        $('.dropdown-item').click(function() {
            var status = $(this).data('status');
            var propertyId = $(this).data('property-id');
            var form = $('#status-form-' + propertyId);
            var token = $('meta[name="csrf-token"]').attr('content');
            var button = $('#btn-' + propertyId);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: {
                    _method: 'PUT',
                    _token: token,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        button.removeClass().addClass('btn btn-' + status.toLowerCase() + ' dropdown-toggle');
                        button.html(status + ' <span class="caret"></span>');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Status updated successfully!',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to update status.',
                            confirmButtonColor: '#d33',
                            confirmButtonText: 'OK'
                        });

                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while updating status.',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });

                }
            });
        });
    });
</script>
@endpush