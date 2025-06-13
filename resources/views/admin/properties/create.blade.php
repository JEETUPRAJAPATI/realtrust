@extends('backend.layouts.app')

@section('title', 'Create Property')

@push('styles')


<link rel="stylesheet" href="{{asset('backend/plugins/bootstrap-select/css/bootstrap-select.css')}}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.5/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
@endpush


@section('content')

<div class="block-header"></div>

<div class="row clearfix">
    <form action="{{route('admin.properties.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="col-sm-12 col-xs-12" style="display:flex;gap:10px;">
            <div class="col-lg-8 col-md-4 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                        <h4 class="text-black mb-0">CREATE PROPERTY</h4>
                    </div>
                    <div class="card-body mt-3">
                        <!-- Property Title -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line">
                                <label class="form-label">Property Title</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                            </div>
                            @error('title')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Owner -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('owner') ? 'focused error' : '' }}">
                                <label>Owner</label>
                                <select name="owner" class="form-control show-tick">
                                    <option value="">-- Please select --</option>
                                    @foreach ($owners as $owner)
                                    <option value="{{ $owner->id }}" {{ old('owner') == $owner->id ? 'selected' : '' }}>
                                        {{ $owner->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('owner')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Purpose -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('purpose') ? 'focused error' : '' }}">
                                <label>Select Purpose</label>
                                <select name="purpose" id="purposeSelect" class="form-control show-tick">
                                    <option value="">-- Please select --</option>
                                    <option value="sell" {{ old('purpose') == 'sell' ? 'selected' : '' }}>Sell</option>
                                    <option value="rent" {{ old('purpose') == 'rent' ? 'selected' : '' }}>Rent</option>
                                    <option value="upcoming_projects" {{ old('purpose') == 'upcoming_projects' ? 'selected' : '' }}>Upcoming Projects</option>
                                </select>
                            </div>
                            @error('purpose')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Furnish Type -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('furnish_type') ? 'focused error' : '' }}">
                                <label>Furnish Type</label>
                                <select name="furnish_type" class="form-control show-tick">
                                    <option value="">-- Please select --</option>
                                    <option value="Fully Furnished" {{ old('furnish_type') == 'Fully Furnished' ? 'selected' : '' }}>Fully Furnished</option>
                                    <option value="Semi Furnished" {{ old('furnish_type') == 'Semi Furnished' ? 'selected' : '' }}>Semi Furnished</option>
                                    <option value="Unfurnished" {{ old('furnish_type') == 'Unfurnished' ? 'selected' : '' }}>Unfurnished</option>
                                </select>
                            </div>
                            @error('furnish_type')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- City -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line">
                                <label>City</label>
                                <select name="city" class="form-control select2">
                                    <option value="">-- Please select --</option>
                                    <option value="57933" selected>Bengaluru</option>
                                </select>
                            </div>
                            @error('city')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Locality -->
                        <div class="form-group form-float mb-2">
                            <label class="form-label">Locality</label>
                            <div class="form-line">
                                <select id="locality" name="locality" class="form-control select2">
                                    <option value="">Select Locality</option>
                                    @foreach($locality as $localityData)
                                    <option value="{{ $localityData->id }}" {{ old('locality') == $localityData->id ? 'selected' : '' }}>{{ $localityData->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('locality')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Society Name -->
                        <div class="form-group form-float mb-2">
                            <label class="form-label">Society Name</label>
                            <div class="form-line">
                                <select class="form-control select2" name="society_name" id="society">
                                </select>
                            </div>
                            @error('society_name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Area -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line">
                                <label class="form-label">Area</label>
                                <input type="number" class="form-control" name="area" value="{{ old('area') }}">
                            </div>
                            @error('area')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Description -->

                        <div class="form-group form-float mb-2">
                            <label for="description" class="col-md-2 col-form-label">Description</label><br>
                            <div class="col-md-10">
                                <textarea name="description" id="tinymce" class="form-control" rows="5">{{ old('description') }}</textarea>
                                @error('description')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <!-- Additional Detail -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line">
                                <label for="additional_detail">Additional Detail</label>
                                <input
                                    type="text"
                                    name="additional_detail"
                                    id="tags"
                                    value="{{ old('additional_detail') }}"
                                    class="form-control"
                                    placeholder="Add tags" />
                            </div>
                            @error('additional_detail')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                        <h4 class="text-black mb-0">Finance Details</h4>
                    </div>
                    <div class="card-body mt-3">
                        <!-- Price Section -->
                        <div class="form-group form-float mb-2" id="priceSection" style="display: none;">
                            <div class="form-line {{ $errors->has('price') ? 'focused error' : '' }}">
                                <label class="form-label">Price</label>
                                <input type="number" class="form-control" name="price" value="{{ old('price') }}" min="0">
                            </div>
                            @error('price')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Price Range Section -->
                        <div class="form-group form-float mb-2" id="priceRangeSection" style="display: none;">
                            <div class="form-line {{ $errors->has('price_range') ? 'focused error' : '' }}">
                                <label class="form-label">Price Range</label>
                                <input type="text" class="form-control" name="price_range" value="{{ old('price_range') }}">
                            </div>
                            @error('price_range')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Maintenance Section -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('maintenance') ? 'focused error' : '' }}">
                                <label class="form-label">Maintenance</label>
                                <input type="number" class="form-control" name="maintenance" value="{{ old('maintenance') }}" min="0">
                            </div>
                            @error('maintenance')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Monthly Rent Section -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('monthly_rent') ? 'focused error' : '' }}">
                                <label class="form-label">Monthly Rent</label>
                                <input type="number" class="form-control" name="monthly_rent" value="{{ old('monthly_rent') }}" min="0">
                            </div>
                            @error('monthly_rent')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Deposit Section -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('deposit') ? 'focused error' : '' }}">
                                <label class="form-label">Deposit</label>
                                <input type="number" class="form-control" name="deposit" value="{{ old('deposit') }}" min="0">
                            </div>
                            @error('deposit')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>


                <!-- Upcoming Projects Section -->
                <div id="upcomingProjectsSection" style="display: none; margin-top: 20px;">
                    <div class="card">
                        <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                            <h4 class="text-black mb-0">Upcoming Project</h4>
                        </div>
                        <div class="card-body mt-3">
                            <!-- PDF File Upload Section -->
                            <div class="form-group form-float mb-2">
                                <div class="form-line {{ $errors->has('pdf_file') ? 'focused error' : '' }}">
                                    <input type="file" name="pdf_file" class="form-control">
                                    <label class="form-label">Upload PDF</label>
                                </div>
                                @error('pdf_file')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- YouTube Video URLs Section -->
                            <div id="youtubeUrlsContainer">
                                <div class="form-group form-float mb-2">
                                    <div class="form-line {{ $errors->has('youtube_urls.*') ? 'focused error' : '' }}">
                                        <input type="text" name="youtube_urls[]" class="form-control" placeholder="YouTube Video URL" value="{{ old('youtube_urls.0') }}">
                                        <label class="form-label">YouTube Video URL</label>
                                    </div>
                                    @error('youtube_urls.*')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Add Another YouTube URL Button -->
                            <button type="button" id="addYoutubeUrl" class="btn btn-primary">Add Another Video URL</button>
                        </div>
                    </div>
                </div>


                <!-- Gallery Image Section -->
                <div id="galleryImageSection" style="display: none; margin-top: 20px;">
                    <div class="card">
                        <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                            <h4 class="text-black mb-0">GALLERY IMAGE</h4>
                        </div>
                        <div class="card-body mt-3">
                            <input id="input-id" type="file" name="gallaryimage[]" class="file" data-preview-file-type="text" multiple>
                            @error('gallaryimage')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                            @error('gallaryimage.*')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                        <h4 class="text-black mb-0">SELECT</h4>
                    </div>

                    <div class="card-body mt-3">
                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('type') ? 'focused error' : '' }}">
                                <label>Select Type</label>
                                <select name="type" class="form-control show-tick">
                                    <option value="">-- Please select --</option>
                                    <option value="apartment" {{ old('type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                                    <option value="villa" {{ old('type') == 'villa' ? 'selected' : '' }}>Villa</option>
                                    <option value="penthouse" {{ old('type') == 'penthouse' ? 'selected' : '' }}>Penthouse</option>
                                    <option value="row house" {{ old('type') == 'row house' ? 'selected' : '' }}>Row House</option>
                                </select>
                            </div>
                            @error('type')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('tenant_type') ? 'focused error' : '' }}">
                                <label>Tenant Type</label>
                                <select name="tenant_type" class="form-control show-tick">
                                    <option value="">-- Please select --</option>
                                    <option value="family" {{ old('tenant_type') == 'family' ? 'selected' : '' }}>Family</option>
                                    <option value="bachelorBoy" {{ old('tenant_type') == 'bachelorBoy' ? 'selected' : '' }}>BachelorBoy</option>
                                    <option value="bachelorGirls" {{ old('tenant_type') == 'bachelorGirls' ? 'selected' : '' }}>bachelorGirls</option>
                                </select>
                            </div>
                            @error('tenant_type')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Bedroom Field -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('bedroom') ? 'focused error' : '' }}">
                                <label class="form-label">Bedroom</label>
                                <input type="number" class="form-control" name="bedroom" min="1" max="5" value="{{ old('bedroom') }}" maxlength="1" title="Please enter a number between 1 and 5">
                            </div>
                            @error('bedroom')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Bathroom Field -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('bathroom') ? 'focused error' : '' }}">
                                <label class="form-label">Bathroom</label>
                                <input type="number" class="form-control" name="bathroom" min="1" max="5" value="{{ old('bathroom') }}" maxlength="1" title="Please enter a number between 1 and 5">
                            </div>
                            @error('bathroom')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Total Floor -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('total_floor') ? 'focused error' : '' }}">
                                <label class="form-label">Total Floor</label>
                                <input type="number" class="form-control" name="total_floor" min="1" max="40" value="{{ old('total_floor') }}" title="Please enter a number between 1 and 40">
                            </div>
                            @error('total_floor')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Floor -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('floor') ? 'focused error' : '' }}">
                                <label class="form-label">Floor</label>
                                <input type="number" class="form-control" name="floor" min="1" max="40" value="{{ old('floor') }}" title="Please enter a number between 1 and 40">
                            </div>
                            @error('floor')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <h5>Amenities</h5>
                        <div class="form-group demo-checkbox">
                            @foreach($amenities as $amenitie)
                            <input type="checkbox" id="amenities-{{$amenitie->id}}" name="amenities[]" class="filled-in chk-col-indigo" value="{{$amenitie->id}}"
                                {{ in_array($amenitie->id, old('amenities', [])) ? 'checked' : '' }} />
                            <label for="amenities-{{$amenitie->id}}">{{$amenitie->name}}</label>
                            @endforeach
                            @error('amenities')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Balcony Field -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('balcony') ? 'focused error' : '' }}">
                                <label class="form-label">Balcony</label>
                                <input type="number" class="form-control" name="balcony" min="1" max="5" value="{{ old('balcony') }}" maxlength="1" title="Please enter a number between 1 and 5">
                            </div>
                            @error('balcony')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('highlight') ? 'focused error' : '' }}">
                                <label>Highlight</label>
                                <select name="highlight" class="form-control show-tick">
                                    <option value="">-- Please select --</option>
                                    <option value="Hot" {{ old('highlight') == 'Hot' ? 'selected' : '' }}>Hot</option>
                                    <option value="Trending" {{ old('highlight') == 'Trending' ? 'selected' : '' }}>Trending</option>
                                    <option value="null" {{ old('highlight') == 'null' ? 'selected' : '' }}>Null</option>
                                </select>
                            </div>
                            @error('highlight')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- BHK Field -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('bhk') ? 'focused error' : '' }}">
                                <label class="form-label">BHK</label>
                                <input type="number" class="form-control" name="bhk" min="1" max="5" value="{{ old('bhk') }}" maxlength="1" title="Please enter a number between 1 and 5">
                            </div>
                            @error('bhk')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Available For Field -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('available_for') ? 'focused error' : '' }}">
                                <label class="form-label">Available For</label>
                                <input type="date" class="form-control" name="available_for" value="{{ old('available_for') }}">
                            </div>
                            @error('available_for')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <h5>Features</h5>
                        <div class="form-group demo-checkbox">
                            @foreach($features as $feature)
                            <input type="checkbox" id="features-{{$feature->id}}" name="features[]" class="filled-in chk-col-indigo" value="{{$feature->id}}"
                                {{ in_array($feature->id, old('features', [])) ? 'checked' : '' }} />
                            <label for="features-{{$feature->id}}">{{$feature->name}}</label>
                            @endforeach
                            @error('features')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Latitude & Longitude Fields -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('latitude') ? 'focused error' : '' }}">
                                <label class="form-label">Latitude</label>
                                <input type="text" class="form-control" name="latitude" value="{{ old('latitude') }}">
                            </div>
                            @error('latitude')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('longitude') ? 'focused error' : '' }}">
                                <label class="form-label">Longitude</label>
                                <input type="text" class="form-control" name="longitude" value="{{ old('longitude') }}">
                            </div>
                            @error('longitude')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Age Field -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('age') ? 'focused error' : '' }}">
                                <label class="form-label">Age</label>
                                <input type="number" class="form-control" name="age" value="{{ old('age') }}">
                            </div>
                            @error('age')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Floor Plan Section -->
                <div id="floorPlanSection" style="display: none;">
                    <div class="card">
                        <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                            <h4 class="text-black mb-0">FLOOR PLAN</h4>
                        </div>
                        <div class="card-body mt-3">
                            <div class="form-group form-float mb-2">
                                <div class="form-line {{ $errors->has('floor_plan') ? 'focused error' : '' }}">
                                    <input type="file" class="form-control" name="floor_plan" value="{{ old('floor_plan') }}">
                                    <label class="form-label">Upload Floor Plan</label>
                                </div>
                                @error('floor_plan')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                        <h4 class="text-black mb-0">Featured Image</h4>
                    </div>
                    <div class="card-body mt-3">
                        <!-- Featured Image Section -->
                        <div class="form-group">
                            <label class="form-label">Choose Featured Image</label>
                            <input type="file" name="image" class="form-control">
                            @error('image')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Button Row -->
                        <div class="d-flex justify-content-between mt-4">
                            <!-- Back Button -->
                            <a href="{{ route('admin.properties.index') }}" class="btn btn-default btn-sm waves-effect">
                                <i class="bi bi-arrow-left-short"></i> BACK
                            </a>

                            <!-- Save Button -->
                            <button type="submit" class="btn btn-primary waves-effect">
                                SAVE
                            </button>
                        </div>
                    </div>
                </div>


            </div>

        </div>
    </form>
</div>

@endsection


@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.5/js/fileinput.min.js"></script>

<script src="{{ asset('backend/plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>
<script src="{{asset('backend/plugins/tinymce/tinymce.js')}}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<!-- Bootstrap Datepicker JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script>
    $('.select2').select2();

    document.addEventListener('DOMContentLoaded', function() {
        const purposeSelect = document.getElementById('purposeSelect');
        const upcomingProjectsSection = document.getElementById('upcomingProjectsSection');
        const galleryImageSection = document.getElementById('galleryImageSection');
        const addYoutubeUrlButton = document.getElementById('addYoutubeUrl');
        const youtubeUrlsContainer = document.getElementById('youtubeUrlsContainer');
        const priceSection = document.getElementById('priceSection');
        const priceRangeSection = document.getElementById('priceRangeSection');

        if (purposeSelect && upcomingProjectsSection && galleryImageSection) {
            // Show/hide sections based on purpose selection
            purposeSelect.addEventListener('change', function() {
                const selectedValue = this.value;

                // Toggle Upcoming Projects Section
                if (selectedValue === 'upcoming_projects') {
                    upcomingProjectsSection.style.display = 'block';
                    galleryImageSection.style.display = 'none';

                    priceRangeSection.style.display = 'block';
                    priceSection.style.display = 'none';
                }
                // Toggle Gallery Image Section for Sell and Rent
                else if (selectedValue === 'sell') {
                    upcomingProjectsSection.style.display = 'none';
                    galleryImageSection.style.display = 'block';
                    floorPlanSection.style.display = 'block'; // Show floor plan

                    priceRangeSection.style.display = 'none';
                    priceSection.style.display = 'block';

                } else if (selectedValue === 'rent') {
                    upcomingProjectsSection.style.display = 'none';
                    galleryImageSection.style.display = 'block';
                    floorPlanSection.style.display = 'none'; // Hide floor plan

                    priceRangeSection.style.display = 'none';
                    priceSection.style.display = 'block';
                }
                // Hide both for other values
                else {
                    upcomingProjectsSection.style.display = 'none';
                    galleryImageSection.style.display = 'none';

                    priceRangeSection.style.display = 'none';
                    priceSection.style.display = 'none';
                }
            });

            // Trigger change for old value handling
            const selectedValue = purposeSelect.value;
            if (selectedValue === 'upcoming_projects') {
                upcomingProjectsSection.style.display = 'block';
                galleryImageSection.style.display = 'none';
            } else if (selectedValue === 'sell') {
                upcomingProjectsSection.style.display = 'none';
                galleryImageSection.style.display = 'block';
                floorPlanSection.style.display = 'block'; // Show floor plan
            } else if (selectedValue === 'rent') {
                upcomingProjectsSection.style.display = 'none';
                galleryImageSection.style.display = 'block';
                floorPlanSection.style.display = 'none'; // Hide floor plan
            } else {
                upcomingProjectsSection.style.display = 'none';
                galleryImageSection.style.display = 'none';
            }

            // Add more YouTube URLs dynamically
            if (addYoutubeUrlButton) {
                addYoutubeUrlButton.addEventListener('click', function() {
                    const newField = `
                <div class="form-group form-float mb-2">
                    <div class="form-line">
                        <input type="text" name="youtube_urls[]" class="form-control" placeholder="YouTube Video URL">
                        <label class="form-label">YouTube Video URL</label>
                    </div>
                </div>`;
                    youtubeUrlsContainer.insertAdjacentHTML('beforeend', newField);
                });
            }
        } else {
            console.error('Required elements not found.');
        }
    });


    $(function() {
        $("#input-id").fileinput();
    });

    $(document).ready(function() {

        var input = document.querySelector("#tags");
        var tagify = new Tagify(input, {
            // Options like maximum tags, delimiters, etc.
            maxTags: 10,
            delimiters: ", ",
            whitelist: ["Example", "Tag", "Input"]
        });

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd', // You can customize the format
            autoclose: true,
            todayHighlight: true
        });


        $('#locality').change(function() {
            const localityId = $(this).val(); // Get the selected locality ID
            const societyDropdown = $('#society'); // Target society dropdown

            // Clear existing options in the society dropdown
            societyDropdown.html('<option value="">Select Society</option>');

            if (localityId) {
                $.ajax({
                    url: '/get-societies', // The API endpoint
                    method: 'POST',
                    data: {
                        locality_id: localityId,
                        _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
                    },
                    beforeSend: function() {
                        // Optionally, disable dropdown or show loader
                        societyDropdown.prop('disabled', true);
                    },
                    success: function(response) {
                        console.log('responsed data', response);

                        if (response.length > 0) {
                            // Append options to the dropdown
                            response.forEach(function(society) {
                                societyDropdown.append(`<option value="${society.id}">${society.name}</option>`);
                                console.log('society', society);
                            });
                        } else {
                            alert('No societies found for the selected locality.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                        alert('An error occurred while fetching societies.');
                    },
                    complete: function() {
                        // Re-enable the dropdown
                        console.log('complete');
                        societyDropdown.prop('disabled', false);
                        societyDropdown.trigger('change');
                    }
                });
            }
        });
    });
</script>

@endpush