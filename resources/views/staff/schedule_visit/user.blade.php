@extends('backend.layouts.app')

@section('title', 'Schedule Visit List')

@push('styles')
<!-- JQuery DataTable Css -->
<link rel="stylesheet" href="{{ asset('backend/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css') }}">
@endpush

@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">PROPERTY USER LIST</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable js-exportable">
                        <thead>
                            <tr>
                                <th>SL.</th>
                                <th>User Name</th>
                                <th>OTP Verification Field Manager</th>
                                <th width="100px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($visiterInfo as $key => $visiter)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $visiter->user->name ?? 'N/A' }}</td>
                                <td>
                                    @if($visiter->otp_verification === 'done')
                                    <span class="badge bg-success">Done</span>
                                    @else
                                    <span class="badge bg-danger">Pending</span>
                                    @endif
                                </td>
                                <td class="text-center" style="display: flex; gap: 8px; justify-content: center;">
                                    <!-- Delete Button with Tooltip -->
                                    <button type="button" class="btn btn-danger btn-sm waves-effect"
                                        onclick="deleteUser({{ $visiter->id }})" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <!-- Hidden Delete Form for Actual Deletion -->
                                    <form action="{{ route('staff.schedule_properties.destroy', $visiter->id) }}"
                                        method="POST" id="del-user-{{ $visiter->id }}" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @push('scripts')

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
                        'Schedule Visit has been deleted.',
                        'success'
                    )
                }
            });
        }
    </script>
    @endpush