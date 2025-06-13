@extends('backend.layouts.app')

@section('title', 'Show Property')


@section('content')
<div class="row">
    <!-- Property Details Section -->
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">SHOW INQUIRIES</h4>
                <a href="{{ route('admin.inquery.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>
            <div class="card-body mt-3">
                <ul class="list-group">
                    <li class="list-group-item">
                        <strong>Name:</strong>
                        <span class="float-end">{{ $inquery->name }}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Email:</strong>
                        <span class="float-end">{{ $inquery->email }}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Phone:</strong>
                        <span class="float-end">{{ $inquery->phone }}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Message:</strong>
                        <span class="float-end">{{ $inquery->message }}</span>
                    </li>
                    <li class="list-group-item">
                        <strong>Status:</strong>
                        <span class="float-end">
                            <form id="status-form-{{ $inquery->id }}" action="{{ route('admin.inquery.updateStatus', ['id' => $inquery->id]) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="btn-group">
                                    <button type="button" id="btn-{{ $inquery->id }}" class="btn btn-{{ strtolower($inquery->status) }} dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ ucfirst($inquery->status) }} <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @foreach(['resolved', 'pending'] as $status)
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item" data-inquery-id="{{ $inquery->id }}" data-status="{{ $status }}">
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


@endsection


@push('scripts')

<script src="https://maps.google.com/maps/api/js?v=3&sensor=false"></script>
<script src="{{ asset('backend/plugins/gmaps/gmaps.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.dropdown-item').click(function() {
            var status = $(this).data('status');
            var contactId = $(this).data('inquery-id');
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