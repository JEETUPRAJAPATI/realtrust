@extends('backend.layouts.app')

@section('title', 'Posts')

@push('styles')

<!-- JQuery DataTable Css -->
<link rel="stylesheet" href="{{ asset('backend/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css') }}">

@endpush

@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">POST LIST</h4>
                <a href="{{ route('staff.posts.create') }}" class="btn btn-primary">
                    CREATE
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL.</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Is Approved</th>
                                <th>Status</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $posts as $key => $post )
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>
                                    @if(Storage::disk('public')->exists('posts/'.$post->image))
                                    <img src="{{Storage::url('posts/'.$post->image)}}" alt="{{$post->title}}" class="img-responsive img-rounded" width="50%">
                                    @endif
                                </td>
                                <td>
                                    <span title="{{$post->title}}">
                                        {{ Str::limit($post->title, 10) }}
                                    </span>
                                </td>
                                <td> @if($post->admin)
                                    {{ strtok($post->admin->name, " ") }}
                                    @else
                                    {{ strtok($post->staff->name, " ") }}
                                    @endif
                                </td>
                                <td>
                                    @foreach($post->categories as $key=>$category)
                                    @if($key!=0)
                                    <span>,</span>
                                    @endif
                                    {{$category->name}}
                                    @endforeach
                                </td>

                                <td>
                                    @if($post->is_approved == true)
                                    <span class="badge bg-success">Approved</span>
                                    @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    @if($post->status == true)
                                    <span class="badge bg-success">Published</span>
                                    @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{route('staff.posts.show',$post->slug)}}" class="btn btn-success btn-sm waves-effect">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{route('staff.posts.edit',$post->slug)}}" class="btn btn-info btn-sm waves-effect">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm waves-effect" onclick="deletePost({{$post->id}})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form action="{{route('staff.posts.destroy',$post->slug)}}" method="POST" id="del-post-{{$post->id}}" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
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
<script src="{{ asset('backend/js/pages/tables/jquery-datatable.js') }}"></script>

<script>
    function deletePost(id) {

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
                document.getElementById('del-post-' + id).submit();
                swal(
                    'Deleted!',
                    'Post has been deleted.',
                    'success'
                )
            }
        })
    }
</script>


@endpush