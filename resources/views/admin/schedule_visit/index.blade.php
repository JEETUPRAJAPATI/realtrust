@extends('backend.layouts.app')

@section('title', ' Schedule Visit List')

@push('styles')
@endpush

@section('content')
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">SCHEDULE VISIT</h4>
                <!-- <a href="{{ route('admin.schedule_visit.create') }}" class="btn btn-primary">
                    Schedule Visit
                </a> -->
            </div>
            <div class="card-body mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable js-exportable">
                        <thead>
                            <tr>
                                <th>SL.</th>
                                <th>Property Name</th>
                                <th>Owner</th>
                                <th>Total Users</th>
                                <th>Timing</th>
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
                                    @if ($visiter->property)
                                    @if ($visiter->property->owner)
                                    <p><strong>User Name:</strong> {{ $visiter->property->owner->name }}</p>
                                    <p><strong>Email:</strong> {{ $visiter->property->owner->email }}</p>
                                    <p><strong>Phone No:</strong> {{ $visiter->property->owner->mobile_no }}</p>
                                    @else
                                    <p>No owner assigned</p>
                                    @endif
                                    @else
                                    <p>No property assigned</p>
                                    @endif
                                </td>
                                <td><span class="badge bg-danger">{{ $visiter->userLists->count()}}</span></td>
                                <td>
                                    @if($visiter && $visiter->timing)
                                    {{ Carbon\Carbon::parse($visiter->timing)->format('l, F j, Y \a\t g:i A') }}
                                    @else
                                    N/A
                                    @endif
                                </td>

                                <td>
                                <div class="text-center d-flex justify-content-center align-items-center">
                                    <!-- Edit Schedule Visit Button -->
                                    <!-- <a href="{{ route('admin.schedule_visit.edit', $visiter->id) }}" class="btn btn-info btn-sm waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit schedule Visit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a> -->

                                    <!-- Delete Button -->
                                    <button type="button" class="btn btn-danger btn-sm waves-effect mx-1" onclick="deleteUser({{ $visiter->id }})" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <form action="{{ route('admin.schedule_properties.destroy', $visiter->id) }}" method="POST" id="del-user-{{ $visiter->id }}" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                    <!-- View Schedule Visit Button -->
                                    @if (!empty($visiter) && isset($visiter->timing))
                                    <!-- <a href="{{ route('admin.schedule_visit.view', $visiter->property_id) }}" class="btn btn-success btn-sm waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" title="View Schedule Visit">
                                        <i class="bi bi-calendar-check"></i>
                                    </a> -->
                                    @else
                                    <!-- Schedule Visit Button -->
                                    <!-- <a href="{{ route('admin.schedule_properties', $visiter->property_id) }}" class="btn btn-warning btn-sm waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" title="Schedule Visit">
                                        <i class="bi bi-airplane-engines"></i>
                                    </a> -->
                                    @endif

                                    <!-- Total Users Button -->
                                    <a href="{{route('admin.schedule_visit.user', $visiter->id)}}" class="btn btn-purple btn-sm waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" title="Total User">
                                        <i class="bi bi-person-fill"></i>
                                    </a>

                                    <!-- Send Template to All Users Button -->
                                    <!--<a href="{{route('admin.schedule_visit.sendTemplateUser', $visiter->property_id)}}" class="btn btn-teal btn-sm waves-effect" data-bs-toggle="tooltip" data-bs-placement="top" title="Send Template All User">-->
                                    <!--    <i class="bi bi-person-circle"></i>-->
                                    <!--</a>-->
                                
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