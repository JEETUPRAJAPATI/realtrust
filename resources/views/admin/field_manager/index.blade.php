@extends('backend.layouts.app')

@section('title', 'FieldManager')
@section('content')

<div class="block-header_"></div>

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="text-black mb-0">FieldManager List</h4>
                    <a href="{{ route('admin.field_manager.create') }}" class="btn btn-primary">
                        CREATE
                    </a>
                </div>
                <div class="card-body mt-3">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable js-exportable">
                            <thead>
                                <tr>
                                    <th>SL.</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach( $field_managers as $key => $field_manager )
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>
                                        @if(Storage::disk('public')->exists('field_manager/'.$field_manager->image))
                                        <img src="{{Storage::url('field_manager/'.$field_manager->image)}}" alt="{{$field_manager->title}}" width="160" class="img-responsive img-rounded">
                                        @else
                                        <img src="{{ asset('assets/img/defaultprofile.png') }}" alt="Default Image" width="100" class="img-responsive img-rounded">
                                        @endif
                                    </td>
                                    <td>{{$field_manager->name}}</td>
                                    <td>{{$field_manager->email}}</td>
                                    <td>{{$field_manager->mobile_no}}</td>
                                    <td>
                                        <div class="text-center d-flex justify-content-center align-items-center">
                                            <a href="{{route('admin.field_manager.edit',$field_manager->id)}}" class="btn btn-info btn-sm waves-effect mx-1">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="{{route('admin.staff.edit',$field_manager->id)}}" class="btn btn-success btn-sm waves-effect mx-1" data-bs-toggle="modal" data-bs-target="#verticalycentered">
                                                <i class="bi bi-key"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm waves-effect mx-1" onclick="deleteUser({{ $field_manager->id}})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form action="{{route('admin.field_manager.destroy',$field_manager->id)}}" method="POST" id="del-user-{{$field_manager->id}}" style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <div class="modal fade" id="verticalycentered" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Change Password</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="passwordUpdateForm" action="{{ route('admin.fieldManagerPassword.update', $field_manager->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="form-group mb-3">
                                                        <label for="password">Enter New Password</label>
                                                        <input type="password" name="password" class="form-control" id="password" placeholder="Enter New Password" required>
                                                        <span class="invalid-feedback error-password"></span>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="confirm_password">Confirm Password</label>
                                                        <input type="password" name="password_confirmation" class="form-control" id="confirm_password" placeholder="Confirm Password" required>
                                                        <span class="invalid-feedback error-password_confirmation"></span>
                                                    </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="button" id="submitBtn" class="btn btn-primary">Update Password</button>
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
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

    <!-- <script src="{{ asset('backend/js/pages/tables/jquery-datatable.js') }}"></script> -->
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
    <script>
        $(document).ready(function() {
            $('#submitBtn').on('click', function() {
                let form = $('#passwordUpdateForm');

                $('.form-control').removeClass('is-invalid'); // Remove previous validation highlights
                $('.invalid-feedback').text(''); // Clear previous errors

                $('#loader').removeClass('d-none').addClass('d-flex');
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#loader').removeClass('d-flex').addClass('d-none');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Password updated successfully!',
                        });
                        form[0].reset(); // Reset the form
                        location.reload(); // Reload the page
                    },
                    error: function(xhr) {
                        $('#loader').removeClass('d-flex').addClass('d-none');
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                let input = $('[name="' + key + '"]');
                                input.addClass('is-invalid'); // Add error highlight
                                $('.error-' + key).text(value[0]); // Show error message
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Something went wrong. Please try again.',
                            });
                        }
                    }

                });
            });
        });
    </script>

    @endpush