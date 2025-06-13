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
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">PROPERTY USER LIST</h4>
            </div>
            <div class="card-body mt-2">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL.</th>
                                <th>Property Name</th>
                                <th>User Name</th>
                                <th>Email Id</th>
                                <th>Status</th>
                                <th width="100px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visiterInfo as $key => $visiter)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ ($visiter->property->title . ' - ' . $visiter->property->unique_id) ?? 'N/A' }}</td>

                                <td>
                                    @if($visiter->users && $visiter->users->name)
                                    {{ $visiter->users->name }}
                                    @else
                                    N/A
                                    @endif
                                </td>
                                <td>{{ $visiter->email ?? 'N/A' }}</td>
                                <td>
                                    @if($visiter->status)
                                    <span class="badge rounded-pill bg-primary">{{ $visiter->status }}</span>
                                    @endif
                                </td>
                                <td class="text-center" style="display:flex;gap: 8px;">
                                    <!-- Delete Button -->
                                    <button type="button" class="btn btn-danger btn-sm waves-effect" onclick="deleteUser({{ $visiter->id }})">
                                        <i class="bi bi-trash-fill"></i> <!-- Font Awesome Trash Icon -->
                                    </button>
                                    <form action="{{ route('staff.schedule_properties.destroy', $visiter->id) }}" method="POST" id="del-user-{{ $visiter->id }}" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                    <!-- Schedule Visit Button -->
                                    <a href="{{ route('staff.schedule_properties.visit', $visiter->property_id) }}" class="btn btn-warning waves-effect waves-float" data-toggle="tooltip" data-placement="top" title="Schedule Visit">
                                    <i class="bi bi-airplane-engines"></i>  <!-- Font Awesome Plane Departure Icon -->
                                    </a>
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