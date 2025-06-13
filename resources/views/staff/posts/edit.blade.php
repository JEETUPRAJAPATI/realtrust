@extends('backend.layouts.app')

@section('title', 'Edit Post')

@push('styles')

@endpush


@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">

<div class="block-header"></div>

<div class="row clearfix">
    <form action="{{route('staff.posts.update',$post->slug)}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="" style="display:flex;gap:10px;">
            <div class="col-lg-8 col-md-4 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="text-black mb-0">EDIT POST</h4>
                        <a href="{{ route('admin.posts.index') }}" class="btn btn-primary">
                            Back
                        </a>
                    </div>
                    <div class="card-body mt-2">

                        <div class="form-group form-float">
                            <div class="form-line">
                                <label class="form-label">Post Title</label>
                                <input type="text" name="title" class="form-control" value="{{$post->title}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="published">Published</label>
                            @if($post->status)
                            @php
                            $checked = 'checked';
                            @endphp
                            @else
                            @php
                            $checked = '';
                            @endphp
                            @endif
                            <input type="checkbox" id="published" name="status" class="filled-in" value="1" {{$checked}} />

                        </div>
                        <div class="form-group">
                            <label for="">Body</label>
                            <textarea name="body" id="summernote" class="form-control" rows="5">{{$post->body}}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="text-black mb-0">SELECT CATEGORY</h4>
                    </div>
                    <div class="card-body mt-2">
                        <div class="form-group form-float">
                            <div class="form-line {{$errors->has('categories') ? 'focused error' : ''}}">
                                <label for="categories">Select Category</label>
                                <select name="categories[]" class="form-control show-tick select2-no-search" id="categories" multiple>
                                    @foreach($categories as $category)
                                    <option value="{{$category->id}}" {{ in_array($category->id, $selectedcategories ?? []) ? 'selected' : '' }}>{{$category->name}}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('categories'))
                                <span class="error-message">{{$errors->first('categories')}}</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group form-float mb-3">
                            <div class="form-line {{$errors->has('tags') ? 'focused error' : ''}}">
                                <label for="tags">Select Tag</label>
                                <select name="tags[]" class="form-control show-tick select2-no-search" id="tags" multiple>
                                    @foreach($tags as $tag)
                                        <option value="{{ $tag->id }}" {{ in_array($tag->id, $selectedtag ?? []) ? 'selected' : '' }}>
                                            {{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has('tags'))
                                <span class="error-message">{{$errors->first('tags')}}</span>
                                @endif
                            </div>
                        </div>



                        <div class="form-group form-float mb-3">
                            @if(Storage::disk('public')->exists('posts/'.$post->image))
                            <div class="form-group mb-3">
                                <img src="{{Storage::url('posts/'.$post->image)}}" id="post-imgsrc-edit" alt="{{$post->title}}" class="img-fluid img-thumbnail rounded"
                                style="max-width: 200px;">
                            </div>
                            @endif
                            <div class="mb-4">
                                <label for="post-image-input-edit" class="form-label">Upload New Image</label>
                                <input type="file" id="post-image-input-edit" name="image" class="form-control">
                                @error('image')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2 waves-effect">
                            <span>Update</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
{{-- SELECTED CATEGORIES --}}
@php
$categories = [];
@endphp
@foreach($post->categories as $category)
@php
$categories[] = $category->id;
@endphp
@endforeach

@endsection


@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<!--<script>-->
<!--    var selectedcategory = <?= json_encode($categories) ?>;-->
<!--    var selectedtags = <?= json_encode($selectedtag) ?>;-->

<!--    $('#categories').selectpicker();-->
<!--    $('#categories').selectpicker('val', selectedcategory);-->


<!--    $('#tags').selectpicker();-->
<!--    $('#tags').selectpicker('val', selectedtags);-->
<!--</script>-->

<!--<script src="{{asset('backend/plugins/tinymce/tinymce.js')}}"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>

<script>
  $(document).ready(function() {
    $('#summernote').summernote({
      height: 200
    });
  });
</script>
<script>
    $(document).ready(function() {
        $('#categories').select2();
        $('#tags').select2();
    });

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
    $('#post-image-btn-edit').on('click', function() {
        $('#post-image-input-edit').click();
    });
    $('#post-image-input-edit').on('change', function() {
        showImage(this, '#post-imgsrc-edit');
    });
</script>

@endpush