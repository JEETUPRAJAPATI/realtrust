@extends('backend.layouts.app')

@section('title', 'Edit FieldManager')

@push('styles')


@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">OTP VARIFICATION</h4>
            </div>
            <div class="card-body mt-3">
                <form action="{{route('field_manager.visiter.update',$field_manager->id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <!-- Properties Dropdown -->


                    <!-- Users Dropdown -->
                    <div class="form-group form-float mb-2">
                        <label class="form-label">User</label>
                        <div class="form-line">
                            <input type="text" class="form-control " name="user_id" value="{{ $field_manager->user->name }}" readonly>
                        </div>
                        @error('user_id')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group form-float mb-2">
                        <label class="form-label">User Mobile No</label>
                        <div class="form-line flex-grow-1" style="display: flex;">
                            <input type="text" class="form-control " name="mobile_no" value="{{ $field_manager->user->mobile_no }}" readonly>
                            <button type="button" id="send-otp-btn" class="btn btn-primary ml-2">Send OTP</button>
                        </div>
                        @error('mobile_no')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <!-- Timing Field -->
                    <div class="mb-3">
                        <label class="form-label">OTP Number</label>
                        <input type="number" name="otp" class="form-control" value="" required>
                        @error('otp')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>


                    <button type="submit" class="btn btn-primary btn-sm m-t-15 waves-effect">
                        <span>Update</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection


@push('scripts')

<script>
    $(document).ready(function() {
        $('#send-otp-btn').click(function() {
            // Disable the button

            var mobile_no = '{{ $field_manager->user->mobile_no }}';
            $(this).prop('disabled', true);
            $(this).text('Sending...');

            // Perform AJAX request
            $.ajax({
                url: '{{ route('field_manager.visiter.send-otp') }}',
                // Update with your route
                type: 'POST',
                data: {
                    mobile_no: mobile_no,
                    type: 'field_manager',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {

                    Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'OTP sent successfully!',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'OK'
                        });
                },
                error: function(xhr) {
                    // Handle error
                    const msg = 'Error sending OTP: ' + (xhr.responseJSON?.message || 'Failed To Send OTP');
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: msg,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                },
                complete: function() {
                    // Re-enable the button after the request is complete
                    $('#send-otp-btn').prop('disabled', false);
                    $('#send-otp-btn').text('Send OTP');
                }
            });
        });
    });
</script>

@endpush