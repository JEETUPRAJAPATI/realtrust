@extends('backend.layouts.app')

@section('title', 'Invoice List')

@push('styles')

@endpush

@section('content')
<?php

use App\Models\Property;
use App\Models\User;
?>

<div class="block-header_"></div>
<div id="loader" class="d-none justify-content-center align-items-center" style="display: none; 
    position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999;">
    <div class="spinner-border text-primary" role="status">
        <!-- <span class="sr-only">Loading...</span> -->
    </div>
</div>

<div class="row clearfix">

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="text-black mb-0">Invoice List</h4>
                    <a href="{{ route('admin.invoice.create') }}" class="btn btn-primary">
                        Generate Invoice
                    </a>
                </div>
                <div class="m-3 align-items-center">
                    <form action="{{ route('admin.invoice.list') }}" method="GET" class="mb-3">
                        <div class="row">
                            <!-- Mobile Number Filter -->
                            <div class="col-md-3">
                                <input type="text" name="mobile_no" class="form-control" placeholder="Search by Mobile Number" value="{{ request('mobile_no') }}">
                            </div>
                            <!-- Invoice Date Filter -->
                            <!-- Status Filter -->
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">Select Payment Type</option>
                                    <option value="token" {{ request('status') == 'token' ? 'selected' : '' }}>Token</option>
                                    <option value="invoice" {{ request('status') == 'invoice' ? 'selected' : '' }}>Invoice</option>
                                </select>
                            </div>
                            <!-- Search Button -->
                            <div class="col-md-3 mt-1">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('admin.invoice.list') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable js-exportable">
                            <thead>
                                <tr>
                                    <th>SL.</th>
                                    <th>User</th>
                                    <th>Invoice Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach( $invoices as $key => $list )
                                <tr>
                                    <td>{{$key+1}}</td>
                                    @php
                                    $property = Property::where('unique_id', $list->property_id)->first();
                                    $user = User::where('id', $list->user_id)->first();
                                    @endphp
                                    <td>{{ $user->name }}-{{ $user->mobile_no }}</td>
                                    <td>{{ \Carbon\Carbon::parse($list->invoice_date)->format('F d, Y') }}</td>
                                    <td>&#8377; {{ number_format($list->total_amount) }}</td>
                                    <td>
                                        @if($list->status == 1)
                                        <span class="badge bg-success">Approved</span>
                                        @else
                                        <span class="badge bg-danger">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $list->payment_type }}
                                    </td>
                                    <td>
                                        <div class="text-center d-flex justify-content-center align-items-center">
                                            <a href="{{ route('admin.payments.invoice.download', $list->id) }}" class="btn btn-info btn-sm waves-effect mx-1">
                                                <i class="bi bi-download"></i>
                                            </a>
                                            <a href="{{ route('admin.invoice.edit', $list->id) }}" class="btn btn-success btn-sm waves-effect mx-1">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <!--<button type="button" class="btn btn-danger btn-sm waves-effect" onclick="deleteStaff({{ $list->id}})">-->
                                            <!--    <i class="bi bi-trash"></i>-->
                                            <!--</button>-->
                                            <!--<form action="{{route('admin.staff.destroy',$list->id)}}" method="POST" id="del-staff-{{$list->id}}" style="display:none;">-->
                                            <!--    @csrf-->
                                            <!--    @method('DELETE')-->
                                            <!--</form>-->
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
    <!-- <script src="{{ asset('backend/js/pages/tables/jquery-datatable.js') }}"></script> -->

    <script>
        function deleteStaff(id) {

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
                    document.getElementById('del-staff-' + id).submit();
                    swal(
                        'Deleted!',
                        'Slider has been deleted.',
                        'success'
                    )
                }
            })
        }
    </script>

    @endpush