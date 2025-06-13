@extends('backend.layouts.app')

@section('title', 'FieldManager')

@push('styles')

<!-- JQuery DataTable Css -->
<link rel="stylesheet" href="{{ asset('backend/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css') }}">

@endpush

@section('content')

<div class="block-header_"></div>

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">Visiter List</h4>
            </div>
            <div class="card-body mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable js-exportable">
                        <thead>
                            <tr>
                                <th>SL.</th>
                                <th>Property Name</th>
                                <!--<th>Owner</th>-->
                                <th>Timing</th>
                                <th>Action</th>
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
                                <!--    @if ($field_manager->owner)-->
                                <!--        <p><strong>Owner Name:</strong> {{ $field_manager->owner->name }}</p>-->
                                <!--        <p><strong>Email:</strong> {{ $field_manager->owner->email }}</p>-->
                                        
                                <!--    @else-->
                                <!--        <p>No owner assigned</p>-->
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
                               
                              
                                <td>
                                    <!-- OTP Verification Button -->
                                    <div class="text-center d-flex justify-content-center align-items-center">
                                        <a href="{{ route('field_manager.visiter.users', $field_manager->id) }}" class="btn btn-purple btn-sm waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" title="Total User">
                                            <i class="bi bi-person-fill"></i>
                                        </a>
                                    </div>
                                </td>
        </tr>
    @endforeach
@endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@push('scripts')
<!-- Custom Js -->
<script src="{{ asset('backend/js/pages/tables/jquery-datatable.js') }}"></script>

<script>
    function deleteUser(id) {

        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {
                document.getElementById('del-user-' + id).submit();
                swal(
                    'Deleted!',
                    'Slider has been deleted.',
                    'success'
                )
            }
        })
    }
</script>


@endpush