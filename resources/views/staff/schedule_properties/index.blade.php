@extends('backend.layouts.app')

@section('title', ' Schedule Visit List')

@push('styles')

<!-- JQuery DataTable Css -->
<link rel="stylesheet" href="{{ asset('backend/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css') }}">

@endpush

@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">Visiter List</h4>
                <a href="{{ route('staff.schedule_visit.create') }}" class="btn btn-primary">
                    Schedule Visit
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable js-exportable">
                        <thead>
                            <tr>
                                <th>SL.</th>
                                <th>Property Name</th>
                                <th>User</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visiterInfo as $key => $visiter)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    @if($visiter->property && $visiter->property->title)
                                    {{$visiter->property->title . ' - ' . $visiter->property->unique_id}}
                                    @else
                                    N/A
                                    @endif
                                </td>
                                <td>
                                    @if ($visiter->users)
                                    <p><strong>User Name:</strong> {{ $visiter->users->name}}</p>
                                    <p><strong>Email:</strong> {{ $visiter->users->email }}</p>
                                    <!--<p><strong>Phone No:</strong> {{ $visiter->users->mobile_no }}</p>-->
                                    @else
                                    <p>No user assigned</p>
                                    @endif
                                </td>
                                <td>
                                    @if ($visiter->status)
                                    @if ($visiter->status === 'pending')
                                    <span class="badge rounded-pill bg-warning text-dark">Pending</span>
                                    @elseif ($visiter->status === 'schedule')
                                    <span class="badge bg-success">Schedule</span>
                                    @elseif ($visiter->status === 'deleted')
                                    <span class="badge rounded-pill bg-danger">Deleted</span>
                                    @else
                                    <span class="badge rounded-pill bg-warning text-dark">Pending</span>
                                    @endif
                                    @else
                                    <span class="badge rounded-pill bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-center d-flex justify-content-center align-items-center">
                                        <!-- Delete Button -->
                                        <button type="button" class="btn btn-danger btn-sm waves-effect mx-2"
                                            onclick="deleteUser({{ $visiter->id }})" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
    
                                        <!-- Hidden Delete Form for Actual Deletion -->
                                        <form action="{{ route('staff.schedule_properties.destroy', $visiter->id) }}"
                                            method="POST" id="del-user-{{ $visiter->id }}" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
    
                                        <!-- Schedule Visit or View Schedule -->
                                        @if (!empty($visiter->schedule_visit_date) && isset($visiter->schedule_visit_date->timing))
                                        <a href="{{ route('staff.schedule_properties.view', $visiter->property_id) }}"
                                            class="btn btn-success btn-sm waves-effect" data-toggle="tooltip"
                                            data-placement="top" title="View Schedule Visit">
                                            <i class="bi bi-calendar-check"></i>
                                        </a>
                                        @else
                                        <a href="{{ route('staff.schedule_properties.visit', $visiter->property_id) }}"
                                            class="btn btn-warning btn-sm waves-effect" data-toggle="tooltip"
                                            data-placement="top" title="Schedule Visit">
                                            <i class="bi bi-calendar-plus"></i>
                                        </a>
                                        @endif
                                    </div>    
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