@extends('backend.layouts.app')
<style>
    #main,
    #footer {
        margin-left: 0px !important;
    }
</style>
@section('title', 'Staff Login')

@section('content')
<section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 d-flex flex-column align-items-center justify-content-center">

                <div class="d-flex justify-content-center py-4">
                    <a href="index.html" class="logo d-flex align-items-center w-auto">
                        <img src="assets/img/logo.png" alt="">
                        <span class="d-none d-lg-block">RealTrust</span>
                    </a>
                </div><!-- End Logo -->

                <div class="card mb-3">

                    <div class="card-body">

                        <div class="pt-4 pb-2">
                            <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                            <p class="text-center small">Enter your details to login</p>
                        </div>
                        <form class="row g-3 needs-validation" enctype="multipart/form-data" novalidate id="sign_in" method="POST" action="{{ route('inquery.submit') }}">
                            @csrf

                            <!-- Property Details Section -->
                            <fieldset class="border p-3 mt-3">
                                <legend class="w-auto px-2">Property Details</legend>

                                <!-- Property Name -->
                                <div class="col-12 pb-3">
                                    <input type="text" class="form-control" name="property_name" placeholder="Property Name"
                                        value="{{ old('property_name', $detial->property->title) }}" required>
                                    <input type="hidden" name="property_id" value="{{ $detial->property->unique_id }}">
                                    @error('property_name')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- City -->
                                <div class="col-12 pb-3">
                                    <input type="text" class="form-control" name="city" placeholder="City"
                                        value="{{ old('city', $detial->property->city) }}" required>
                                    @error('city')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Locality -->
                                <div class="col-12 pb-3">
                                    <input type="text" class="form-control" name="locality" placeholder="Locality"
                                        value="{{ old('locality', $detial->property->locality) }}" required>
                                    @error('locality')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Society -->
                                <div class="col-12 pb-3">
                                    <input type="text" class="form-control" name="society" placeholder="Society"
                                        value="{{ old('society', $detial->property->society_name) }}" required>
                                    @error('society')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Price -->
                                <div class="col-12 pb-3">
                                    <input type="number" class="form-control" name="price" placeholder="Price"
                                        value="{{ old('price', $detial->property->price) }}" required>
                                    @error('price')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Address -->
                                <div class="col-12 pb-3">
                                    <textarea class="form-control" name="address" rows="3" placeholder="Address" required>{{ old('address', $detial->property->description) }}</textarea>
                                    @error('address')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Final Rent -->
                                <div class="col-12 pb-3">
                                    <input type="number" class="form-control" name="final_rent" placeholder="Final Rent"
                                        value="{{ old('final_rent') }}" required>
                                    @error('final_rent')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Deposit -->
                                <div class="col-12 pb-3">
                                    <input type="number" class="form-control" name="deposit" placeholder="Deposit"
                                        value="{{ old('deposit') }}" required>
                                    @error('deposit')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Maintenance -->
                                <div class="col-12 pb-3">
                                    <input type="number" class="form-control" name="maintenance" placeholder="Maintenance per Month"
                                        value="{{ old('maintenance') }}">
                                    @error('maintenance')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </fieldset>

                            <!-- Personal Details Section -->
                            <fieldset class="border p-3 mt-3">
                                <legend class="w-auto px-2">Personal Details</legend>

                                <!-- Name -->
                                <div class="col-12 pb-3">
                                    <input type="text" class="form-control" name="name" placeholder="Name"
                                        value="{{ old('name', $user->name) }}" required>
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                    @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 pb-3">
                                    <!-- Aadhaar Card Image Upload -->
                                    <label for="aadhaar_image">Aadhaar Card Image</label>
                                    <input type="file" class="form-control" id="aadhaar_image" name="aadhaar_image" accept="image/*" onchange="previewImage(this, 'aadhaarPreview')">
                                    <div class="mt-3">
                                        <img id="aadhaarPreview"
                                            src="{{ old('aadhaar_image', $user->aadhaar_card ? Storage::url('users/' . $user->id . '/documents/' . $user->aadhaar_card) : '#') }}"
                                            alt="Aadhaar Card Preview"
                                            style="max-width: 100%; {{ $user->aadhaar_card ? '' : 'display: none;' }}">
                                    </div>
                                </div>

                                <div class="col-12 pb-3">
                                    <!-- PAN Card Image Upload -->
                                    <label for="pan_image">PAN Card Image</label>
                                    <input type="file" class="form-control" id="pan_image" name="pan_image" accept="image/*" onchange="previewImage(this, 'panPreview')">
                                    <div class="mt-3">
                                        <img id="panPreview"
                                            src="{{ old('pan_image', $user->pan_card ? Storage::url('users/' . $user->id . '/documents/' . $user->pan_card) : '#') }}"
                                            alt="PAN Card Preview"
                                            style="max-width: 100%; {{ $user->pan_card ? '' : 'display: none;' }}">
                                    </div>
                                </div>

                                <div class="col-12 pb-3">
                                    <!-- Agreement Image Upload -->
                                    <label for="agreement_image">Agreement Image</label>
                                    <input type="file" class="form-control" id="agreement_image" name="agreement_image" accept="image/*" onchange="previewImage(this, 'agreementPreview')">
                                    <div class="mt-3">
                                        <img id="agreementPreview"
                                            src="{{ old('agreement_image', $user->agreement ? Storage::url('users/' . $user->id . '/documents/' . $user->agreement) : '#') }}"
                                            alt="Agreement Preview"
                                            style="max-width: 100%; {{ $user->agreement ? '' : 'display: none;' }}">
                                    </div>
                                </div>



                            </fieldset>

                            <!-- Submit Button -->
                            <div class="col-12 mt-3">
                                <button class="btn btn-primary w-100" type="submit">Submit</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
        function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'block'; // Make the image visible
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            // Reset the preview if no file is selected
            preview.src = '#';
            preview.style.display = 'none';
        }
    }

</script>
@endsection
@push('script')


@endpush