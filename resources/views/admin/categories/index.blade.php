@extends('backend.layouts.app')

@section('title', 'Categories')
@section('content')


<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">CATEGORY LIST</h4>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                    CREATE
                </a>
            </div>
            <div class="card-body mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable js-exportable">
                        <thead>
                            <tr>
                                <th>SL.</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Post Num</th>
                                <th>Slug</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $categories as $key => $category )
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>
                                    @if(Storage::disk('public')->exists('category/thumb/'.$category->image))
                                    <img src="{{Storage::url('category/thumb/'.$category->image)}}" alt="{{$category->name}}" width="60" class="img-responsive img-rounded">
                                    @endif
                                </td>
                                <td>{{$category->name}}</td>
                                <td>{{$category->posts->count()}}</td>
                                <td>{{$category->slug}}</td>
                                <td class="text-center">
                                    <a href="{{route('admin.categories.edit',$category->id)}}" class="btn btn-success btn-sm waves-effect">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm waves-effect" onclick="deleteCategory({{$category->id}})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form action="{{route('admin.categories.destroy',$category->id)}}" method="POST" id="del-category-{{$category->id}}" style="display:none;">
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

<script src="{{ asset('backend/js/pages/tables/jquery-datatable.js') }}"></script>
<script>
    function deleteCategory(id) {
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
                document.getElementById('del-category-' + id).submit();
                swal(
                    'Deleted!',
                    'Category has been deleted.',
                    'success'
                )
            }
        })
    }
</script>


@endpush