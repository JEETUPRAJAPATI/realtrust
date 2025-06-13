@extends('backend.layouts.app')

@section('title', 'Generate Invoice')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />

<!--<style>-->
<!--    .select2-selection__rendered {-->
<!--    border: 1px solid #ced4da !important;-->
<!--}-->

<!--</style>-->
@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">Add Invoice Detail</h4>
                <a href="{{ route('admin.invoice.list') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>

            <!-- Card Body -->
            <div class="card-body mt-3">
                <form action="{{ route('admin.invoice.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('user') ? 'focused error' : '' }}">
                            <label class="form-label">Customer<span class="text-danger">*</span>:</label>
                            
                            <div class="select-wrapper" >
                                <select class="form-control select2" name="user">
                                    <option value="">Select Customer</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user') == $user->id ? 'selected' : '' }}>{{ $user->name }} - {{ $user->mobile_no }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        @error('user')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('user_add') ? 'focused error' : '' }}">
                            <label class="form-label">Customer Address:</label>
                            <textarea class="form-control" placeholder="Enter Address" name="user_add" ></textarea>
                        </div>
                        @error('user_add')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('name') ? 'focused error' : '' }}">
                            <label class="form-label ">Property<span class="text-danger">*</span>:</label>
                            <div class="select-wrapper" >
                            <select class="form-control select2" name="property" >
                                <option value="">Select Property</option>
                                @foreach($properties as $property)
                                <option value="{{$property->unique_id}}" {{ old('property') == $property->unique_id ? 'selected' : '' }}>{{$property->title}}-{{$property->unique_id}}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>
                        @error('property')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('seller_add') ? 'focused error' : '' }}">
                            <label class="form-label">Seller Address:</label>
                            <textarea class="form-control" value="{{old('seller_add')}}" name="seller_add" placeholder="Enter Address" ></textarea>
                        </div>
                        @error('seller_add')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <!-- Name Input -->
                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('name') ? 'focused error' : '' }}">
                            <label class="form-label">Invoice Date<span class="text-danger">*</span>:</label>
                            <input type="date" name="invoice_date" value="{{old('invoice_date')}}" class="form-control" value="{{ old('invoice_date') }}" >
                        </div>
                        @error('invoice_date')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email Input -->
                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('gst_percent') ? 'focused error' : '' }}">
                            <label>GST %<span class="text-danger">*</span>:</label>
                            <input type="number" placeholder="18" value="{{old('gst_percent')}}" class="form-control" name="gst_percent" step="0.01" >
                        </div>
                        @error('gst_percent')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Mobile Input -->
                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('total_amount') ? 'focused error' : '' }}">
                            <label>Total Amount<span class="text-danger">*</span>:</label>
                            <input type="number" placeholder="12000" value="{{old('total_amount')}}" name="total_amount" class="form-control" step="0.01" >
                        </div>
                        @error('total_amount')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    @php
                        $paymentModes = ['UPI', 'Bank', 'Cash', 'Cheque', 'Card', 'Wallet', 'Net Banking', 'Other'];
                    @endphp
                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('payment_mode') ? 'focused error' : '' }}">
                            <label>Payment Mode<span class="text-danger">*</span>:</label>
                            <select class="form-control" name="payment_mode">
                                @foreach($paymentModes as $mode)
                                    <option value="{{ $mode }}" {{ old('payment_mode') == $mode ? 'selected' : '' }}>
                                        {{ $mode }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                    </div>
                    <div class="form-group form-float mb-4">
                        <div class="form-line {{ $errors->has('total_amount') ? 'focused error' : '' }}">
                            <label>Status<span class="text-danger">*</span>:</label>
                            <select class="form-control" name="status">
                                <option value="0">Pending</option>
                                <option value="1">Approved</option>
                            </select>
                        </div>
                        
                    </div>
                    

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary  waves-effect">
                        <span>SAVE</span>
                    </button>

                </form>

            </div>
        </div>
    </div>
</div>

@endsection


@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
    });
    });
</script>

@endpush