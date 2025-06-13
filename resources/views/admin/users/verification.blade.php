@extends('backend.layouts.app')

@section('title', 'Show Property')

@push('styles')
<style>
    
</style>
@endpush
@section('content')


<div class="row">
    <!-- Property Details Section -->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">Verify User</h4>
                <a href="{{ route('admin.user.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>
            <div class="card-body mt-3">
                <ul class="list-group">
                    <!-- User Name -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>User Name:</strong>
                        <span>{{ $user->name }}</span>
                    </li>

                    <!-- User Email -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>User Email:</strong>
                        <span>{{ $user->email }}</span>
                    </li>

                    <!-- User Mobile No -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>User Mobile No:</strong>
                        <span>{{ $user->mobile_no }}</span>
                    </li>

                    <!-- Company Name -->
                    <!-- <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Company Name:</strong>
                        <span>{{ $user->company_name }}</span>
                    </li> -->

                    <!-- Aadhaar Card -->
                    <!-- <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Aadhaar Card:</strong>
                        <a href="{{ Storage::url($user->aadhaar_card) }}" target="_blank" class="btn btn-link btn-sm">View Aadhaar Card</a>
                    </li> -->

                    <!-- PAN Card -->
                    <!-- <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>PAN Card:</strong>
                        <a href="{{ Storage::url($user->pan_card) }}" target="_blank" class="btn btn-link btn-sm">View PAN Card</a>
                    </li> -->

                    <!-- Employee ID -->
                    <!-- <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Employee ID:</strong>
                        <span>{{ $user->employee_id }}</span>
                    </li> -->

                    <!-- Agreement -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Agreement:</strong>
                        @if(pathinfo($user->agreement, PATHINFO_EXTENSION) == 'pdf')
                        <a href="{{ Storage::url('users/agreement/' . $user->agreement) }}" target="_blank" class="btn btn-link btn-sm">View Agreement (PDF)</a>
                        @elseif(pathinfo($user->agreement, PATHINFO_EXTENSION) == 'docx')
                        <a href="{{ Storage::url('users/agreement/' . $user->agreement) }}" target="_blank" class="btn btn-link btn-sm">View Agreement (DOCX)</a>
                        @else
                        <a href="{{ Storage::url('users/agreement/' . $user->agreement) }}" target="_blank" class="btn btn-link btn-sm">View Agreement</a>
                        @endif
                    </li>
                    <?php $key = $user->id; ?>
                    <!-- Status -->
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Verified?--:</strong>
                        <form id="verification-form-{{$key}}" action="{{ route('admin.user.updateStatus', ['id' => $user->id]) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="verification" id="verification-input-{{$key}}">

                            @php
                            $verificationStatus = $user->verification;
                            @endphp

                            <div class="form-check form-switch" style="cursor: pointer;">
                                <input class="form-check-input" type="checkbox" id="verificationSwitch{{$key}}" {{ $verificationStatus == 1 ? 'checked' : '' }} onclick="updateVerificationStatus({{$key}}, {{ $verificationStatus == 1 ? '0' : '1' }})">
                                <!-- <span class="status-text badge {{ $verificationStatus == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $verificationStatus == 1 ? 'Verified' : 'Not Verified' }}
                                </span> -->

                            </div>

                        </form>

                    </li>
                </ul>
            </div>

        </div>
        <div class="card">
            <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable js-exportable">
                            <thead>
                                <tr>
                                    <th>SL.</th>
                                    <th>Company Name</th>
                                    <th>Employee ID</th>
                                    <th>Aadhar</th>
                                    <th>Pan Card</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($user->userDocuments as $key => $document)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $document->company_name }}</td>
                                    <td>{{ $document->employee_id }}</td>
                                    <td><a href="{{ Storage::url('users/' . $document->user_id . '/documents/' . $document->aadhaar_card) }}" target="_blank" class="btn btn-link btn-sm">View Aadhaar Card</a></td>
                                    <td>
                                        <a href="{{ Storage::url('users/' . $document->user_id . '/documents/' . $document->pan_card) }}" target="_blank" class="btn btn-link btn-sm">View PAN Card</a>
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
<script>
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