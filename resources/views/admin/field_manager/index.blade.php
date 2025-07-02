@extends('backend.layouts.app')

@section('title', 'FieldManager')
@section('content')

<div class="block-header_"></div>

<div class="row clearfix">
    <div class="col-lg-12">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">FieldManager List</h4>
                <a href="{{ route('admin.field_manager.create') }}" class="btn btn-primary">CREATE</a>
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
                            @foreach($field_managers as $key => $field_manager)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>
                                    @if(Storage::disk('public')->exists('field_manager/'.$field_manager->image))
                                    <img src="{{ Storage::url('field_manager/'.$field_manager->image) }}" alt="{{ $field_manager->name }}" width="100" class="img-responsive img-rounded">
                                    @else
                                    <img src="{{ asset('assets/img/defaultprofile.png') }}" alt="Default Image" width="100" class="img-responsive img-rounded">
                                    @endif
                                </td>
                                <td>{{ $field_manager->name }}</td>
                                <td>{{ $field_manager->email }}</td>
                                <td>{{ $field_manager->mobile_no }}</td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="{{ route('admin.field_manager.edit', $field_manager->id) }}" class="btn btn-info btn-sm mx-1">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button class="btn btn-success btn-sm mx-1" data-bs-toggle="modal" data-bs-target="#passwordModal-{{ $field_manager->id }}">
                                            <i class="bi bi-key"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm mx-1" onclick="deleteUser({{ $field_manager->id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <form action="{{ route('admin.field_manager.destroy', $field_manager->id) }}" method="POST" id="del-user-{{ $field_manager->id }}" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- Modal --}}
                            <div class="modal fade" id="passwordModal-{{ $field_manager->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Change Password - {{ $field_manager->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="passwordUpdateForm-{{ $field_manager->id }}" action="{{ route('admin.fieldManagerPassword.update', $field_manager->id) }}" method="POST">
                                                @csrf
                                                <div class="form-group mb-3">
                                                    <label>Enter New Password</label>
                                                    <input type="password" name="password" class="form-control" required>
                                                    <span class="invalid-feedback error-password"></span>
                                                </div>
                                                <div class="form-group">
                                                    <label>Confirm Password</label>
                                                    <input type="password" name="password_confirmation" class="form-control" required>
                                                    <span class="invalid-feedback error-password_confirmation"></span>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary submit-password-btn" data-id="{{ $field_manager->id }}">Update Password</button>
                                                </div>
                                            </form>
                                        </div>
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
                swal('Deleted!', 'User has been deleted.', 'success');
            }
        })
    }

    $(document).ready(function() {
        $('.submit-password-btn').click(function() {
            const id = $(this).data('id');
            const form = $('#passwordUpdateForm-' + id);
            const submitBtn = $(this);

            form.find('.form-control').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(res) {
                    Swal.fire('Success!', 'Password updated successfully!', 'success');
                    $('#passwordModal-' + id).modal('hide');
                    form[0].reset();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, msg) {
                            const input = form.find('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.next('.invalid-feedback').text(msg[0]);
                        });
                    } else {
                        Swal.fire('Error!', 'Something went wrong. Try again.', 'error');
                    }
                }
            });
        });
    });
</script>

@endpush