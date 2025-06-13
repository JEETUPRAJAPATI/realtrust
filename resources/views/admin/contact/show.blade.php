@extends('backend.layouts.app')

@section('title', 'Show Property')


@section('content')
<div class="row">
    <!-- Property Details Section -->
    <div class="col-12">
        <div class="card">

            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">SHOW CONTACT</h4>
                <a href="{{ route('admin.contact.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>
            <div class="card-body mt-3">
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Name:</strong>
                        <span class="float-end">{{ $contact->name }}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Email:</strong>
                        <span class="float-end">{{ $contact->email }}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Phone:</strong>
                        <span class="float-end">{{ $contact->phone }}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Message:</strong>
                        <span class="float-end">{{ $contact->message }}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Status:</strong>
                        <span class="float-end">
                            <form id="status-form-{{ $contact->id }}" action="{{ route('admin.contact.updateStatus', ['id' => $contact->id]) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="btn-group">
                                    <button type="button" id="btn-{{ $contact->id }}" class="btn btn-{{ strtolower($contact->status) }} dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ ucfirst($contact->status) }} <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @foreach(['resolved', 'pending'] as $status)
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item" data-contact-id="{{ $contact->id }}" data-status="{{ $status }}">
                                                {{ ucfirst($status) }}
                                            </a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </form>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        $('.dropdown-item').click(function() {
            var status = $(this).data('status');
            var contactId = $(this).data('contact-id');
            var form = $('#status-form-' + contactId);
            var token = $('meta[name="csrf-token"]').attr('content');
            var button = $('#btn-' + contactId);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: {
                    _method: 'PUT',
                    _token: token,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        button.removeClass().addClass('btn btn-' + status.toLowerCase() + ' dropdown-toggle');
                        button.html(status + ' <span class="caret"></span>');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Status updated successfully!',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                                    icon: 'error',
                                    title: 'Oops!',
                                    text: 'Failed to update status.',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An error occurred while updating status.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                }
            });
        });
    });
</script>
@endpush