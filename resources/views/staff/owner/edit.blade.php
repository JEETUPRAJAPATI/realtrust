@extends('backend.layouts.app')

@section('title', 'Edit Owner')

@push('styles')


@endpush


@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0"> EDIT OWNER</h4>
                <a href="{{ route('staff.owner.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left-short"></i> BACK
                </a>
            </div>
            <div class="card-body mt-2">
                <form action="{{route('staff.owner.update',$owners->id)}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group form-float">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <div class="form-line">
                            <input type="text" name="name" placeholder=" Enter Owner's Name " class="form-control" value="{{ old('name', $owners->name) }}">
                        </div>
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group form-float">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <div class="form-line">
                            <input type="text" name="email" placeholder="Enter Email" class="form-control" value="{{ old('email', $owners->email) }}">
                        </div>
                        @error('email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group form-float">
                        <div class="form-line {{ $errors->has('mobile') ? 'focused error' : '' }}">
                            <label class="form-label">Mobile</label>
                            <input type="text" placeholder=" Enter Owner's Mobile Number " name="mobile" class="form-control" value="{{ old('mobile', $owners->mobile_no) }}">
                        </div>
                        @error('mobile')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                    <!-- <div class="form-group form-float">
                        <label class="form-label">Agreement</label>
                        <div class="form-line">
                            <input type="file" name="agreement" class="form-control" value="">
                        </div>
                        @error('agreement')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div> -->
                    @if(Storage::disk('public')->exists('owner/agreement/' . $owners->agreement))
                    <!-- <div class="form-group">
                        @if(pathinfo($owners->agreement, PATHINFO_EXTENSION) == 'pdf')
                        <a href="{{ Storage::url('owner/agreement/' . $owners->agreement) }}" target="_blank" class="btn btn-primary">View Agreement (PDF)</a>
                        @elseif(pathinfo($owners->agreement, PATHINFO_EXTENSION) == 'docx')
                        <a href="{{ Storage::url('owner/agreement/' . $owners->agreement) }}" target="_blank" class="btn btn-primary">View Agreement (DOCX)</a>
                        @else
                        <a href="{{ Storage::url('owner/agreement/' . $owners->agreement) }}" target="_blank" class="btn btn-primary">View Agreement</a>
                        @endif
                    </div> -->
                    @endif
                    @if(Storage::disk('public')->exists('owners/'.$owners->image))
                    <div class="form-group mt-3">
                        <img src="{{ Storage::url('owners/'.$owners->image) }}" id="owner-imgsrc-edit" alt="{{ $owners->name }}" class="img-thumbnail"
                            style="max-width: 200px;">
                    </div>
                    @endif
                    <!-- Image Upload Button -->
                    <div class="mb-4">
                        <label for="owner-image-btn-edit" class="form-label">Upload New Image</label>
                        <input type="file" id="owner-image-btn-edit" name="image" class="form-control">
                        @error('image')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary  waves-effect">
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
    function showImage(fileInput, imgID) {
        if (fileInput.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $(imgID).attr('src', e.target.result);
                $(imgID).attr('alt', fileInput.files[0].name);
            }
            reader.readAsDataURL(fileInput.files[0]);
        }
    }
    // $('#owner-image-btn-edit').on('click', function() {
    //     $('#owner-image-input-edit').click();
    // });
    $('#owner-image-btn-edit').on('change', function() {
        showImage(this, '#owner-imgsrc-edit');
    });
</script>

@endpush