@extends('backend.layouts.app')
@section('title', 'Show Property')
@push('styles')

@endpush
@section('content')

<div class="block-header"></div>

<div class="row clearfix">
    <!-- Property Details Section -->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">VERIFY OWNER</h4>
                <a href="{{ route('staff.owner.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>
            <div class="card-body mt-3">
                <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>User Name:</strong>
                        <span class="right">{{$user->name}}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>User Email:</strong>
                        <span class="right">{{$user->email}}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>User Mobile No:</strong>
                        <span class="right">{{$user->mobile_no}}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Company Name:</strong>
                        <span class="right">{{ $user->company_name }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Electricity Bill:</strong>
                        <span class="right"><a href="{{ Storage::url('owners/electricity_bill' . $user->id . '/documents/' . $user->electricity_bill) }}" target="_blank" class="btn btn-link btn-sm">View Electricity Bill</a></td></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>PAN Card:</strong>
                        <span class="right"><span class="right"><a href="{{ Storage::url('owners/pan_card' . $user->id . '/documents/' . $user->pan_card) }}" target="_blank" class="btn btn-link btn-sm">View PAN Card</a></td></span></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Employee ID:</strong>
                        <span class="right">{{ $user->employee_id }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>Status:</strong>
                        

                        <form id="verification-form" action="{{ route('staff.owner.updateStatus', ['id' => $user->id]) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="verification" id="verification-input">
                            @php
                            $verificationStatus = $user->verification;
                        @endphp

                        <div class="form-check form-switch" style="cursor: pointer;">
                            <input class="form-check-input" type="checkbox" id="verificationSwitch" 
                                {{ $verificationStatus == 1 ? 'checked' : '' }} 
                                onchange="updateVerificationStatus(this.checked ? 1 : 0)">
                        </div>
                        </form>

                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
            <h4 class="text-black mb-0">Agreement</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable js-exportable">
                    <thead>
                        <tr>
                            <th>SL.</th>
                            <th>Property Id</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agreements as $key => $document)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $document->property_id }}</td>
                            <td>
                                <a href="{{ route('staff.owner.agreement_logs', $document->property_id) }}">
                                    Upload Agreement<!-- Bootstrap Icon -->
                                </a><br>
                                <!-- Button to trigger modal -->
                                <a href="#" data-bs-toggle="modal" data-bs-target="#agreementModal{{ $key }}">
                                    View Agreement Details
                                </a>
                                <!-- Modal -->
                                <div class="modal fade" id="agreementModal{{ $key }}" tabindex="-1" aria-labelledby="agreementModalLabel{{ $key }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="agreementModalLabel{{ $key }}">Agreement Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <table class="table table-bordered">
                                                    <tbody>
                                                        <tr>
                                                            <th>Property ID</th>
                                                            <td>{{ $document->property_id }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Owner ID</th>
                                                            <td>{{ $document->owner_id }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Rent</th>
                                                            <td>{{ $document->rent }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Deposit</th>
                                                            <td>{{ $document->deposit }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Maintenance</th>
                                                            <td>{{ $document->monthly_maintenance }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Contract Duration</th>
                                                            <td>{{ $document->contract_duration }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Renewal Increment</th>
                                                            <td>{{ $document->contract_renewal_increment }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Cleaning Charges</th>
                                                            <td>{{ $document->painting_deep_cleaning_charges }}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Notice Period</th>
                                                            <td>{{ $document->notice_period }}</td>
                                                        </tr>

                                                        <tr>
                                                            <th>Created At</th>
                                                            <td>{{ $document->created_at }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
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

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.updateVerificationStatus = function(status) {
            document.getElementById('verification-input').value = status;
            document.getElementById('verification-form').submit();
        }
    });
</script>
@endpush