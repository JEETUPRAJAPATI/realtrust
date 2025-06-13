@extends('backend.layouts.app')

@section('title', 'Edit Property')

@push('styles')
<link rel="stylesheet" href="{{asset('backend/plugins/bootstrap-select/css/bootstrap-select.css')}}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.5/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<!-- CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush


@section('content')

<div class="block-header"></div>

<div class="row clearfix">
    <form action="{{ route('admin.properties.update', $property->slug) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="col-sm-12 col-xs-12" style="display:flex;gap:10px;">
            <div class="col-lg-8 col-md-4 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                        <h4 class="text-black mb-0">Edit PROPERTY</h4>
                    </div>
                    <div class="card-body mt-3">
                        <!--<div class="form-group form-float mb-2 {{ $errors->has('title') ? 'has-error' : '' }}">-->
                        <!--    <div class="form-line">-->
                        <!--        <label class="form-label">Property Title <span class="text-danger">*</span></label>-->
                        <!--        <input type="text" placeholder="Enter Property Title" name="title" class="form-control" value="{{ old('title', $property->title) }}" disabled>-->
                        <!--    </div>-->
                        <!--    @if($errors->has('title'))-->
                        <!--    <span class="help-block">{{ $errors->first('title') }}</span>-->
                        <!--    @endif-->
                        <!--</div>-->

                        <!-- owner -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('owner') ? 'focused error' : '' }}">
                                <label>Owner <span class="text-danger">*</span></label>
                                <select name="owner" class="form-control show-tick" disabled>
                                    <option value="">-- Please select --</option>
                                    @foreach ($owners as $owner)
                                    <option value="{{ $owner->id }}" {{ old('owner',$owner->id) == $property->owner_id ? 'selected' : '' }}>
                                        {{ $owner->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('owner')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Select Purpose -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('purpose') ? 'focused error' : '' }}">
                                <label>Select Purpose <span class="text-danger">*</span></label>
                                <select name="purpose" id="purposeSelect" class="form-control show-tick">
                                    <option value="">-- Please select --</option>
                                    <option value="sell" {{ old('purpose', $property->purpose) == 'Sell' ? 'selected' : '' }}>Sell</option>
                                    <option value="rent" {{ old('purpose', $property->purpose) == 'Rent' ? 'selected' : '' }}>Rent</option>
                                    <option value="upcoming_projects" {{ old('purpose', $property->purpose) == 'upcoming_projects' ? 'selected' : '' }}>Upcoming Projects</option>
                                </select>
                            </div>
                            @error('purpose')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Furnish Type -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('furnish_type') ? 'focused error' : '' }}">
                                <label>Furnish Type <span class="text-danger">*</span></label>
                                <select name="furnish_type" class="form-control show-tick">
                                    <option value="">-- Please select --</option>
                                    <option value="Fully Furnished" {{ old('furnish_type', $property->furnish_type) == 'Fully Furnished' ? 'selected' : '' }}>Fully Furnished</option>
                                    <option value="Semi Furnished" {{ old('furnish_type', $property->furnish_type) == 'Semi Furnished' ? 'selected' : '' }}>Semi Furnished</option>
                                    <option value="Unfurnished" {{ old('furnish_type', $property->furnish_type) == 'Unfurnished' ? 'selected' : '' }}>Unfurnished</option>
                                </select>
                            </div>
                            @error('furnish_type')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- City -->
                        <div class="form-group form-float mb-2">
                            <div class="form-line">
                                <label>City <span class="text-danger">*</span></label>
                                <select name="city" class="form-control select2">
                                    <option value="">-- Please select --</option>
                                    <option value="{{ old('city', $property->city) }}" selected>Bengaluru</option>
                                </select>
                            </div>
                            @error('city')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group form-float mb-2">
                            <label class="form-label">Locality <span class="text-danger">*</span></label>
                            <div class="form-line">
                                <select id="locality" name="locality8" class="form-control select2" disabled>
                                    <option value="">Select Locality</option>
                                    @foreach($locality as $localityData)
                                    <option value="{{ $localityData->id }}"
                                        {{ old('locality', $property->locality) == $localityData->id ? 'selected' : '' }}>
                                        {{ $localityData->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="hidden" name="locality" value="{{ old('locality', $property->locality) }}">
                            @error('locality')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group form-float mb-2">
                            <label class="form-label">Society name <span class="text-danger">*</span></label>
                            <div class="form-line">
                                <select class="form-control  select2" name="society_name" id="society">
                                <option value="">Select Society Name</option>    
                            </select>
                            </div>
                            @error('society_name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- <div class="form-group form-float mb-2">
                        <div class="form-line">
                            <input type="text" class="form-control" name="society_name" value="{{ old('society_name',$property->society_name) }}">
                            <label class="form-label">Society name </label>
                        </div>
                        @error('society_name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div> -->

                        <div class="form-group form-float mb-2">
                            <div class="form-line">
                                <label class="form-label">Area <span class="text-danger">*</span></label>
                                <input type="number" placeholder="Enter Area" class="form-control" name="area" value="{{ old('area', $property->area) }}">
                            </div>
                            @error('area')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="tinymce">Description <span class="text-danger">*</span></label>
                            <div class="mb-2">
                                <textarea name="description" placeholder="Enter Description" id="tinymce" class="form-control" rows="5">{{ old('description', $property->description) }}</textarea>
                                @error('description')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-line">
                                <label for="additional_detail">Additional Detail <span class="text-danger">*</span></label>
                                
                            <select name="additional_detail[]" id="tags1" class="form-control" multiple>
                                <option value="">-- Please select --</option>
                                    @foreach ($additionalsDetail as $detail)
                                    <option value="{{ $detail->name }}" 
                                        {{ in_array($detail->name, explode(',', $property->additional_details)) ? 'selected' : '' }}>
                                        {{ $detail->name }}
                                    </option>
    
                                    @endforeach    
                            </select>
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
                        <div class="form-group form-float mb-2 {{ $errors->has('price') ? 'has-error' : '' }}" id="priceSection" style="display: none;">
                            <div class="form-line">
                                <label class="form-label">Price <span class="text-danger">*</span></label>
                                <input type="text" placeholder="Enter Price" name="price" class="form-control" value="{{ old('price', $property->price) }}">
                            </div>
                            @if($errors->has('price'))
                            <span class="help-block">{{ $errors->first('price') }}</span>
                            @endif
                        </div>

                        <div class="form-group form-float mb-2" id="priceRangeSection" style="display: none;">
                            <div class="form-line">
                                <label class="form-label">Price Range <span class="text-danger">*</span></label>
                                <input type="text" placeholder="Enter Price Range" class="form-control" name="price_range" value="{{ old('price_range',$property->price_range) }}">
                            </div>
                            @error('price_range')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div id="finance_detail" style="display: none;">
                        <div class="form-group form-float mb-2 {{ $errors->has('maintenance') ? 'has-error' : '' }}">
                            <div class="form-line">
                                <label class="form-label">Maintenance <span class="text-danger">*</span></label>
                                <input type="number" name="maintenance" placeholder="Enter Maintenance" class="form-control" value="{{ old('maintenance', $property->maintenance) }}" >
                            </div>
                            @if($errors->has('maintenance'))
                            <span class="help-block">{{ $errors->first('maintenance') }}</span>
                            @endif
                        </div>

                        <div class="form-group form-float mb-2 {{ $errors->has('deposit') ? 'has-error' : '' }}">
                            <div class="form-line">
                                <label class="form-label">Deposit <span class="text-danger">*</span></label>
                                <input type="number" name="deposit" placeholder="Enter Deposite"  class="form-control" value="{{ old('deposit', $property->deposit) }}" >
                            </div>
                            @if($errors->has('deposit'))
                            <span class="help-block">{{ $errors->first('deposit') }}</span>
                            @endif
                        </div>


                        <div class="form-group form-float mb-2 {{ $errors->has('monthly_rent') ? 'has-error' : '' }}">
                            <div class="form-line">
                                <label class="form-label">Monthly Rent <span class="text-danger">*</span></label>
                                <input type="number" placeholder="Enter Monthly Rent" name="monthly_rent" class="form-control" value="{{ old('monthly_rent', $property->monthly_rent) }}" >
                            </div>
                            @if($errors->has('monthly_rent'))
                            <span class="help-block">{{ $errors->first('monthly_rent') }}</span>
                            @endif
                        </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Projects Section -->
                <div id="upcomingProjectsSection" style="display: none; margin-top: 20px;">
                    <div class="card">
                        <!-- <div class="header">
                    <h2></h2>
                </div> -->
                        <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                            <h4 class="text-black mb-0">Upcoming Project</h4>
                        </div>
                        <div class="card-body mt-3">
                            <div class="form-group form-float mb-2">
                                <div class="form-line">
                                    <input type="file" name="pdf_file" class="form-control">
                                    <label class="form-label">Upload PDF</label>
                                </div>
                                @error('pdf_file')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div id="youtubeUrlsContainer">
                                <div class="form-group form-float mb-2">
                                    <div class="form-line">
                                        <label class="form-label">YouTube Video URL</label>
                                        <input type="text" name="youtube_urls[]" class="form-control" placeholder="YouTube Video URL">
                                    </div>
                                </div>
                            </div>
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
                            <div class="gallery-box" id="gallerybox">
                                @foreach($property->gallery as $gallery)
                                <div class="gallery-image-edit" id="gallery-{{ $gallery->id }}">
                                    <button type="button" data-id="{{ $gallery->id }}" class="btn btn-danger btn-sm delete-gallery-image">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                    <img  class="img-fluid img-thumbnail rounded"
                                     src="{{ Storage::url('property/' .$property->owner_id. '/' . $gallery->property->unique_id . '/gallery/' . $gallery->name) }}" alt="{{ $gallery->name }}">
                                </div>
                                @endforeach
                                <input type="file" name="galleryimage[]" id="input-id" class="file" multiple>
                            </div>
                            <!-- <div class="gallery-box">
                            <hr>
                        </div> -->
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
                                <label>Select Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-control show-tick">
                                    <option value="">-- Please select --</option>
                                    <option value="apartment" {{ old('type', strtolower($property->type)) == 'apartment' ? 'selected' : '' }}>Apartment</option>
                                    <option value="villa" {{ old('type', strtolower($property->type)) == 'villa' ? 'selected' : '' }}>Villa</option>
                                    <option value="penthouse" {{ old('type', strtolower($property->type)) == 'penthouse' ? 'selected' : '' }}>Penthouse</option>
                                    <option value="row house" {{ old('type', strtolower($property->type)) == 'row house' ? 'selected' : '' }}>Row House</option>
                                </select>
                            </div>
                            @error('type')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- <div class="form-group form-float mb-2">
                        <div class="form-line {{ $errors->has('door_facing') ? 'focused error' : '' }}">
                            <label>Door Facing</label>
                            <select name="door_facing" class="form-control show-tick">
                                <option value="">-- Please select --</option>
                                <option value="north" {{ old('door_facing',strtolower($property->door_facing)) == 'north' ? 'selected' : '' }}>North</option>
                                <option value="east" {{ old('door_facing',strtolower($property->door_facing)) == 'east' ? 'selected' : '' }}>East</option>
                                <option value="south" {{ old('door_facing',strtolower($property->door_facing)) == 'south' ? 'selected' : '' }}>South</option>
                                <option value="west" {{ old('door_facing',strtolower($property->door_facing)) == 'west' ? 'selected' : '' }}>West</option>
                            </select>
                        </div>
                        @error('door_facing')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div> -->
                    <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('tenant_type') ? 'focused error' : '' }}">
                                <label>Tenant Type <span class="text-danger">*</span></label>
                                <select name="tenant_type" class="form-control show-tick">
                                    <option value="">-- Please select --</option>
                                    <option value="family" {{ old('tenant_type',$property->tenant_type) == 'family' ? 'selected' : '' }}>Family</option>
                                    <option value="bachelorBoy" {{ old('tenant_type',$property->tenant_type) == 'bachelorBoy' ? 'selected' : '' }}>BachelorBoy</option>
                                    <option value="bachelorGirls" {{ old('tenant_type',$property->tenant_type) == 'bachelorGirls' ? 'selected' : '' }}>BachelorGirls</option>
                                </select>
                            </div>
                            @error('tenant_type')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group form-float mb-2">
                            <div class="form-line">
                                <label class="form-label">Bedroom <span class="text-danger">*</span></label>
                                <input type="number" placeholder="Number of Bedroom" class="form-control" name="bedroom" min="1" max="5" value="{{ old('bedroom', $property->bedroom) }}">
                            </div>
                            @error('bedroom')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group form-float mb-2">
                            <div class="form-line">
                                <label class="form-label">Bathroom <span class="text-danger">*</span></label>
                                <input type="number" placeholder="Number of Bathroom" class="form-control" name="bathroom" min="1" max="5" value="{{ old('bathroom', $property->bathroom) }}">
                            </div>
                            @error('bathroom')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group form-float mb-2">
                            <div class="form-line">
                                <label class="form-label">Balcony</label>
                                <input type="number" class="form-control" placeholder="Number of Balcony"  name="balcony" min="1" max="5" value="{{ old('balcony', $property->balcony) }}">
                            </div>
                            @error('balcony')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group form-float mb-2">
                            <div class="form-line">
                                <label class="form-label">Total Floor</label>
                                <input type="number" placeholder="Number of Floors" class="form-control" name="total_floor" min="1" max="40" value="{{ old('total_floor', $property->total_floor) }}" oninput="if(this.value > 40) this.value = 40;" title="Please enter a number between 1 and 20">
                            </div>
                            @error('bathroom')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group form-float mb-2">
                            <div class="form-line {{ $errors->has('highlight') ? 'focused error' : '' }}">
                                <label>Highlight</label>
                                <select name="highlight" class="form-control show-tick">
                                    <option value="">-- Please select --</option>
                                    <option value="Hot" {{ old('highlight',$property->highlight_type) == 'Hot' ? 'selected' : '' }}>Hot</option>
                                    <option value="Trending" {{ old('highlight',$property->highlight_type) == 'Trending' ? 'selected' : '' }}>Trending</option>
                                    <!-- <option value="Null" {{ old('highlight',$property->highlight_type) == 'Null' ? 'selected' : '' }}>Null</option> -->
                                </select>
                            </div>
                            @error('highlight')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group form-float mb-2">
                            <div class="form-line">
                                <label class="form-label">Floor <span class="text-danger">*</span></label>
                                <input type="number" placeholder="Number of Floors" class="form-control" name="floor" min="1" max="40" value="{{ old('floor', $property->floor) }}" oninput="if(this.value > 40) this.value = 40;" title="Please enter a number between 1 and 40">
                            </div>
                            @error('bathroom')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <h5>Amenities <span class="text-danger">*</span></h5>
                        <div class="form-group demo-checkbox">
                            @foreach($amenities as $amenity)
                            <input type="checkbox" id="amenities-{{ $amenity->id }}" name="amenities[]" class="filled-in chk-col-indigo" value="{{ $amenity->id }}"
                                @foreach($property->amenities as $checked)
                            {{ $checked->id == $amenity->id ? 'checked' : '' }}
                            @endforeach
                            />
                            <label for="amenities-{{ $amenity->id }}">{{ $amenity->name }}</label>
                            @endforeach
                            @error('amenities')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                        <div class="form-group form-float mb-2">
                            <div class="form-line">
                                <label class="form-label">BHK <span class="text-danger">*</span></label>
                                <input type="number" placeholder="Enter BHK" class="form-control" name="bhk" min="1" max="5" value="{{ old('bhk', $property->bhk) }}">
                            </div>
                            @error('bhk')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group form-float mb-2">
                            <div class="form-line">
                                <label class="form-label">Available For <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="available_for" value="{{ old('available_for', $property->available_for) }}">
                            </div>
                            @error('available_for')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <h5>Features <span class="text-danger">*</span></h5>
                        <div class="form-group demo-checkbox">
                            @foreach($features as $feature)
                            <input type="checkbox" id="features-{{ $feature->id }}" name="features[]" class="filled-in chk-col-indigo" value="{{ $feature->id }}"
                                @foreach($property->features as $checked)
                            {{ $checked->id == $feature->id ? 'checked' : '' }}
                            @endforeach
                            />
                            <label for="features-{{ $feature->id }}">{{ $feature->name }}</label>
                            @endforeach

                            @error('features')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        

                        <div class="form-group form-float mb-2">
                            <div class="form-line">
                                <label class="form-label">Age <span class="text-danger">*</span></label>
                                <input type="number" placeholder="Age of Property" class="form-control" name="age" value="{{ old('age', $property->age ) }}">
                            </div>
                            @error('age')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>
                <div id="floorPlanSection" style="display: none;">
                    <div class="card">
                        <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                            <h4 class="text-black mb-0">FLOOR PLAN</h4>
                        </div>
                        <div class="card-body mt-3">
                            <div class="form-group gallery-image-edit">
                                @if(Storage::disk('public')->exists('property/' . $property->owner_id . '/' . $property->unique_id .'/'. $property->floor_plan) && $property->floor_plan)
                                <img src="{{ Storage::url('property/' . $property->owner_id . '/' . $property->unique_id .'/'. $property->floor_plan) }}" alt="{{ $property->title }}"  class="img-fluid img-thumbnail rounded"
                                ><br>
                                @endif
                                <input type="file" name="floor_plan">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                        <h4 class="text-black mb-0">FEATURED IMAGE</h4>
                    </div>
                    <div class="card-body mt-3">
                        <div class="form-group gallery-image-edit">
                            @if(Storage::disk('public')->exists('property/' . $property->owner_id . '/' . $property->unique_id .'/'. $property->image))
                            <img src="{{ Storage::url('property/' . $property->owner_id . '/' . $property->unique_id .'/'. $property->image) }}" alt="{{ $property->title }}"  class="img-fluid img-thumbnail rounded"
                            ><br>
                            @endif
                            <input type="file" name="image">
                        </div>

                        {{-- BUTTON --}}
                        <div class="d-flex justify-content-between mt-4">
                            <!-- Back Button -->
                            <a href="{{ route('staff.properties.index') }}" class="btn btn-default btn-sm waves-effect">
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
<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#tags1').select2();
    });
</script>

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

                priceRangeSection.style.display = 'block';
                priceSection.style.display = 'none';

            } else if (selectedValue === 'sell') {
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
            } else {
                upcomingProjectsSection.style.display = 'none';
                galleryImageSection.style.display = 'none';

                priceRangeSection.style.display = 'none';
                priceSection.style.display = 'none';
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

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });



    $(document).ready(function() {

        const selectedLocality = "{{ old('locality', $property->locality ?? '') }}";
        const selectedSociety = "{{ old('society_name', $property->society_name ?? '') }}";

        if (selectedLocality) {
            $('#locality').val(selectedLocality).trigger('change');

            $.ajax({
                url: '/get-societies',
                method: 'POST',
                data: {
                    locality_id: selectedLocality,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.length > 0) {
                        response.forEach(function(society) {
                            $('#society').append(
                                `<option value="${society.id}" ${
                                society.id == selectedSociety ? 'selected' : ''
                            }>${society.name}</option>`
                            );
                        });
                    }
                }
            });
        }


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
        $('.gallery-image-edit button').on('click', function(e) {
            e.preventDefault();

            var id = $(this).data('id');
            var image = $('#gallery-' + id + ' img').attr('alt');

            // Confirm deletion (optional)
            if (!confirm("Are you sure you want to delete this image?")) {
                return;
            }
            // Send POST request to delete the image
            $.ajax({
                url: "{{ route('admin.gallery-delete') }}", // Laravel route for deletion
                type: "POST",
                data: {
                    id: id,
                    image: image,
                    _token: "{{ csrf_token() }}" // Ensure CSRF token is included
                },
                success: function(response) {
                    if (response.msg === true) {
                        // Remove the image container dynamically
                        $('#gallery-' + id).fadeOut('fast', function() {
                            $(this).remove();
                        });

                        // Display a success message
                        toastr.success("Image deleted successfully!");
                    } else {
                        toastr.error("Failed to delete the image. Please try again.");
                    }
                },
                error: function() {
                    toastr.error("An error occurred. Please try again.");
                }
            });
        });
        var input = document.querySelector("#tags");
        var tagify = new Tagify(input, {
            // Options like maximum tags, delimiters, etc.
            maxTags: 10,
            delimiters: ", ",
            whitelist: ["Example", "Tag", "Input"]
        });

    })

    $(function() {
        // Multiple images preview in browser
        var imagesPreview = function(input, placeToInsertImagePreview) {
            if (input.files) {
                var filesAmount = input.files.length;
                for (i = 0; i < filesAmount; i++) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        $('<div class="gallery-image-edit" id="gallery-perview-' + i + '"><img src="' + event.target.result + '" height="106" width="173"/></div>').appendTo(placeToInsertImagePreview);
                    }
                    reader.readAsDataURL(input.files[i]);
                }
            }
        };
        $('#gallaryimageupload').on('change', function() {
            imagesPreview(this, 'div#gallerybox');
        });
    });

    $(document).on('click', '#galleryuploadbutton', function(e) {
        e.preventDefault();
        $('#gallaryimageupload').click();
    })
</script>

<script src="{{asset('backend/plugins/tinymce/tinymce.js')}}"></script>
<script>

</script>

@endpush