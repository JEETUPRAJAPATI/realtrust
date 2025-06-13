@extends('backend.layouts.app')

@section('title', 'Users')

@push('styles')

<!-- JQuery DataTable Css -->
<link rel="stylesheet" href="{{ asset('backend/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css') }}">
<style>
    .verification-circle .circle-icon {
        width: 18px;
        height: 18px;
        border-radius: 30%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
    }

    .verification-circle.verified .circle-icon {
        background-color: green;
    }

    .verification-circle.not-verified .circle-icon {
        background-color: red;
    }
</style>
@endpush

@section('content')

<div class="block-header_"></div>

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0"> USER LIST</h4>
                <a href="{{ route('staff.user.create') }}" class="btn btn-primary">
                    CREATE
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable js-exportable">
                        <thead>
                            <tr>
                                <th>SL.</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Verifyed</th>
                                <th width="100px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $users as $key => $user )
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>
                                    @if($user->image !== NULL)
                                    <img src="{{Storage::url('users/'.$user->image)}}" alt="{{$user->title}}" width="100" class="img-responsive img-rounded">
                                    @else
                                    <img src="{{ asset('assets/img/defaultprofile.png') }}" alt="Default Image" width="100" class="img-responsive img-rounded">
                                    @endif
                                </td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td>
                                <form id="verification-form" action="{{ route('admin.user.updateStatus', ['id' => $user->id]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="verification" id="verification-input">
                                        @if($user->verification == 1)
                                        <span class="badge bg-success text-light badge-clickable" onclick="updateVerificationStatus(0)">Verified</span>
                                        @else
                                        <span class="badge bg-danger text-light badge-clickable" onclick="updateVerificationStatus(1)">Not Verified</span>
                                        @endif
                                    </form>
                                </td>
                                <td>
                                <div class="text-center d-flex justify-content-center align-items-center">
                                    <a href="{{route('staff.user.edit',$user->id)}}" class="btn btn-info btn-sm">
                                        <i class="bi bi-pencil"></i> <!-- Bootstrap Icon for edit -->
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteUser({{ $user->id }})">
                                        <i class="bi bi-trash"></i> <!-- Bootstrap Icon for delete -->
                                    </button>
                                    <form action="{{route('staff.user.destroy',$user->id)}}" method="POST" id="del-user-{{$user->id}}" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <a href="{{route('staff.user.verify',$user->id)}}" class="btn btn-info btn-sm" data-toggle="tooltip" data-placement="top" title="Verify User">
                                        <i class="bi bi-patch-check"></i> <!-- Bootstrap Icon for verified user -->
                                    </a>
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
                    'User has been deleted.',
                    'success'
                )
            }
        })
    }
    document.addEventListener('DOMContentLoaded', function() {
        window.updateVerificationStatus = function(key, status) {
            const inputId = `verification-input-${key}`;
            const formId = `verification-form-${key}`;

            document.getElementById(inputId).value = status;
            document.getElementById(formId).submit();
        }
    });
</script>


@endpush