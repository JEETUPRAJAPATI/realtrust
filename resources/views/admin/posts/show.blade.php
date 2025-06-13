@extends('backend.layouts.app')

@section('title', 'Show Post')

@push('styles')
<style>
    img {
        max-width: 70em;
    }
</style>
@endpush
@section('content')

<div class="block-header"></div>

<div class="row clearfix">

    <div class="col-lg-8 col-md-4 col-sm-12 col-xs-12">
        <div class="card">
            <!-- Card Header -->
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">Show Post</h4>
            </div>

            <!-- Card Content -->
            <div class="card-content">
                <!-- Post Title and Metadata -->
                <div class="card-header-info">
                    <h5 class="card-title mb-2">{{ $post->title }}</h5>
                    <p class="text-muted mb-0">
                        <small>Posted by <strong>{{ $post->user->name ?? 'Unknown' }}</strong> on {{ $post->created_at->toFormattedDateString() }}</small>
                    </p>
                </div>

                <!-- Post Body -->
                <div class="card-body mt-4">
                    {!! $post->body !!}
                </div>
            </div>
        </div>


        {{-- COMMENTS --}}

    </div>
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">SELECTED CATEGORY</h4>
            </div>
            <div class="card-body mt-3">
                @foreach($post->categories as $category)
                <span class="label bg-cyan">{{$category->name}}</span>
                @endforeach
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">SELECTED TAGS</h4>
            </div>
            <div class="card-body mt-3">
                @foreach($post->tags as $tag)
                <span class="label bg-green">{{$tag->name}}</span>
                @endforeach
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">FEATURED IMAGE</h4>
            </div>
            <div class="card-body mt-3">

                <img class="img-responsive img-rounded" width="50%" src="{{Storage::url('posts/'.$post->image)}}" alt="">
                <div class="mt-2">
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-danger btn-lg" data-toggle="tooltip" data-placement="top" title="Back to Posts">
                        <i class="bi bi-arrow-left-circle-fill"></i> <!-- Bootstrap Icon for Back -->
                        <span>BACK</span>
                    </a>

                    <a href="{{ route('admin.posts.edit', $post->slug) }}" class="btn btn-info btn-lg" data-toggle="tooltip" data-placement="top" title="Edit Post">
                        <i class="bi bi-pencil-fill"></i> <!-- Bootstrap Icon for Edit -->
                        <span>EDIT</span>
                    </a>
                </div>



            </div>
        </div>
    </div>

</div>


@endsection


@push('scripts')

<script>



</script>


@endpush