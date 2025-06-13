@extends('backend.layouts.app')

@section('title', 'Create Post')
@section('content')

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
<div class="block-header"></div>

<div class="row clearfix">
    <form action="{{route('staff.posts.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="col-sm-12 col-xs-12" style="display:flex;gap:10px;">
            <div class="col-lg-8 col-md-4 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="text-black mb-0">CREATE POST</h4>
                    </div>
                    <div class="card-body mt-2">

                        <div class="form-group form-float">
                            <div class="form-line">
                                <label class="form-label">Post Title</label>
                                <input type="text" name="title" class="form-control" value="{{old('title')}}">
                            </div>
                            @if ($errors->has('title'))
                            <span class="text-danger">{{ $errors->first('title') }}</span>
                            @endif
                        </div>

                        <div class="form-group">
                            <input type="checkbox" id="published" name="status" class="filled-in" value="1" />
                            <label for="published">Published</label>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="">Body</label>
                            <textarea name="body" id="summernote" class="form-control" rows="5">{{old('body')}}</textarea>
                            @if ($errors->has('body'))
                            <span class="text-danger">{{ $errors->first('body') }}</span>
                            @endif
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
                                <label>Select Category</label>
                                <select name="categories[]" class="form-control show-tick select2-no-search" id="categories" multiple>
                                    @foreach($categories as $category)
                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('categories'))
                                <span class="text-danger">{{ $errors->first('categories') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group form-float">
                            <div class="form-line {{$errors->has('tags') ? 'focused error' : ''}}">
                                <label>Select Tag</label>
                                <select name="tags[]" class="form-control show-tick select2-no-search" id="tags" multiple>
                                    @foreach($tags as $tag)
                                    <option value="{{$tag->id}}">{{$tag->name}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('tags'))
                                <span class="text-danger">{{ $errors->first('tags') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <label for="form-label">Featured Image</label>
                            <input type="file" name="image" class="form-control">
                            @if ($errors->has('image'))
                            <span class="text-danger">{{ $errors->first('image') }}</span>
                            @endif
                        </div>
                        <a href="{{ route('admin.posts.index') }}" class="btn btn-danger btn-lg m-t-15 waves-effect">
                            <i class="bi bi-arrow-left"></i> <!-- Bootstrap icon for "back" -->
                            <span>BACK</span>
                        </a>

                        <button type="submit" class="btn btn-indigo btn-lg m-t-15 waves-effect">
                            <i class="bi bi-save"></i> <!-- Bootstrap icon for "save" -->
                            <span>SAVE</span>
                        </button>

                    </div>
                </div>
            </div>
    </form>
</div>
</div>
@endsection


@push('scripts')

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
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
</script>
@endpush