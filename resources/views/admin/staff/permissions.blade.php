@extends('backend.layouts.app')

@section('title', 'Edit Slider')

@push('styles')


@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">STAF PERMISSIONS</h4>
                <a href="{{ route('admin.staff.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                    <div class="tab-pane p-4">
                            <h4 class=" text-primary">Edit Permission</h4>
                            <dl class="row">
                            <dt class="col-sm-3">Owner</dt>
                            <dd class="col-sm-9">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" name="status" type="checkbox" role="switch" 
                                               id="status-{{ $permission->id }}" 
                                               value="{{ $permission->owner }}" 
                                               data-id="{{ $permission->staff_id }}" 
                                               data-url="{{ route('admin.update-permission') }}" 
                                               data-token="{{ csrf_token() }}" 
                                               data-type="owner"
                                               @if ($permission->owner == '1') checked @endif 
                                               onclick="status_update(this)">
                                    </div>

                            </dd>
                            <dt class="col-sm-3">Owner's Number</dt>
                            <dd class="col-sm-9">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" name="status" type="checkbox" role="switch" 
                                               id="status-{{ $permission->id }}" 
                                               value="{{ $permission->owner_number }}" 
                                               data-id="{{ $permission->staff_id }}" 
                                               data-url="{{ route('admin.update-permission') }}" 
                                               data-token="{{ csrf_token() }}" 
                                               data-type="owner_number"
                                               @if ($permission->owner_number == '1') checked @endif 
                                               onclick="status_update(this)">
                                    </div>

                            </dd>
                            <dt class="col-sm-3">Users</dt>
                            <dd class="col-sm-9">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" name="status" type="checkbox" role="switch" 
                                               id="status-{{ $permission->id }}" 
                                               value="{{ $permission->property }}" 
                                               data-id="{{ $permission->staff_id }}" 
                                               data-url="{{ route('admin.update-permission') }}" 
                                               data-token="{{ csrf_token() }}" 
                                               data-type="property"
                                               @if ($permission->property == '1') checked @endif 
                                               onclick="status_update(this)">
                                    </div>

                            </dd>
                            <dt class="col-sm-3">Property</dt>
                            <dd class="col-sm-9">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" name="status" type="checkbox" role="switch" 
                                               id="status-{{ $permission->id }}" 
                                               value="{{ $permission->fieldManager_list }}" 
                                               data-id="{{ $permission->staff_id }}" 
                                               data-url="{{ route('admin.update-permission') }}" 
                                               data-token="{{ csrf_token() }}" 
                                               data-type="fieldManager_list"
                                               @if ($permission->fieldManager_list == '1') checked @endif 
                                               onclick="status_update(this)">
                                    </div>

                            </dd>
                            <dt class="col-sm-3">FieldManager List</dt>
                            <dd class="col-sm-9">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" name="status" type="checkbox" role="switch" 
                                               id="status-{{ $permission->id }}" 
                                               value="{{ $permission->schedule_visit }}" 
                                               data-id="{{ $permission->staff_id }}" 
                                               data-url="{{ route('admin.update-permission') }}" 
                                               data-token="{{ csrf_token() }}" 
                                               data-type="schedule_visit"
                                               @if ($permission->schedule_visit == '1') checked @endif 
                                               onclick="status_update(this)">
                                    </div>

                            </dd>
                            <dt class="col-sm-3">Schedule Visit</dt>
                            <dd class="col-sm-9">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input" name="status" type="checkbox" role="switch" 
                                               id="status-{{ $permission->id }}" 
                                               value="{{ $permission->visiter_list }}" 
                                               data-id="{{ $permission->staff_id }}" 
                                               data-url="{{ route('admin.update-permission') }}" 
                                               data-token="{{ csrf_token() }}" 
                                               data-type="visiter_list"
                                               @if ($permission->visiter_list == '1') checked @endif 
                                               onclick="status_update(this)">
                                    </div>

                            </dd>
                            <dt class="col-sm-3">Visiter List</dt>
                            <dd class="col-sm-9">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" name="status" type="checkbox" role="switch" 
                                               id="status-{{ $permission->id }}" 
                                               value="{{ $permission->recording }}" 
                                               data-id="{{ $permission->staff_id }}" 
                                               data-url="{{ route('admin.update-permission') }}" 
                                               data-token="{{ csrf_token() }}" 
                                               data-type="recording"
                                               @if ($permission->recording == '1') checked @endif 
                                               onclick="status_update(this)">
                                    </div>

                            </dd>
                            
                            <dt class="col-sm-3">Post List</dt>
                            <dd class="col-sm-9">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" name="status" type="checkbox" role="switch" 
                                               id="status-{{ $permission->id }}" 
                                               value="{{ $permission->recording }}" 
                                               data-id="{{ $permission->staff_id }}" 
                                               data-url="{{ route('admin.update-permission') }}" 
                                               data-token="{{ csrf_token() }}" 
                                               data-type="post_list"
                                               @if ($permission->post_list == '1') checked @endif 
                                               onclick="status_update(this)">
                                    </div>

                            </dd>
                            <dt class="col-sm-3">Inquiry List</dt>
                            <dd class="col-sm-9">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" name="status" type="checkbox" role="switch" 
                                               id="status-{{ $permission->id }}" 
                                               value="{{ $permission->recording }}" 
                                               data-id="{{ $permission->staff_id }}" 
                                               data-url="{{ route('admin.update-permission') }}" 
                                               data-token="{{ csrf_token() }}" 
                                               data-type="inquiry_list"
                                               @if ($permission->inquiry_list == '1') checked @endif 
                                               onclick="status_update(this)">
                                    </div>

                            </dd>
                            <dt class="col-sm-3">Settings</dt>
                            <dd class="col-sm-9">
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input class="form-check-input" name="status" type="checkbox" role="switch" 
                                               id="status-{{ $permission->id }}" 
                                               value="{{ $permission->settings }}" 
                                               data-id="{{ $permission->staff_id }}" 
                                               data-url="{{ route('admin.update-permission') }}" 
                                               data-token="{{ csrf_token() }}" 
                                               data-type="settings"
                                               @if ($permission->settings == '1') checked @endif 
                                               onclick="status_update(this)">
                                    </div>

                            </dd>
                    </div>
            </div>
        </div>
    </div>
</div>

@endsection


@push('scripts')

<script>
    function showImage(fileInput, imgID) {
        if (fileInput.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $(imgID).attr('src', e.target.result);
                $(imgID).attr('alt', fileInput.files[0].name);
            }
            reader.readAsDataURL(fileInput.files[0]);
        }
    }
    $('#staff-image-btn-edit').on('click', function() {
        $('#staff-image-input-edit').click();
    });
    $('#staff-image-input-edit').on('change', function() {
        showImage(this, '#staff-imgsrc-edit');
    });
</script>
<script>
  function status_update(checkbox) {
    // Prevent default action
    event.preventDefault();

    // Prepare the data to send
    const status = checkbox.checked ? 1 : 0;
    const permissionId = checkbox.getAttribute('data-id');
    const url = checkbox.getAttribute('data-url');
    const token = checkbox.getAttribute('data-token');
    const type = checkbox.getAttribute('data-type');

    // Make the AJAX POST request
    $.ajax({
        url: url,
        type: 'POST',
        data: {
            _token: token,
            id: permissionId,
            status: status,
            type: type
        },
        success: function(response) {
            if (response.success === 200) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message || 'Status updated successfully!',
                    confirmButtonText: 'OK',
                    timer: 2000
                });
                // Reflect the checkbox status without refreshing
                checkbox.checked = status === 1;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Failed to update status.',
                    confirmButtonText: 'Try Again'
                });
                // Revert checkbox state if the update failed
                checkbox.checked = !checkbox.checked;
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while updating the status.',
                confirmButtonText: 'OK'
            });
            // Revert checkbox state in case of error
            checkbox.checked = !checkbox.checked;
        }
    });
}

</script>

@endpush