@extends('backend.layouts.app')

@section('title', 'Features')
@section('content')

<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">FEATURE LIST</h4>
                <a href="{{ route('admin.features.create') }}" class="btn btn-primary">
                    CREATE
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped datatable" id="myTable">
                        <thead>
                            <tr>
                                <th>SL.</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>SL.</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach( $features as $key => $feature )
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>
                                    @if(Storage::disk('public')->exists('feature/'.$feature->image))
                                    <img src="{{Storage::url('feature/'.$feature->image)}}" alt="{{$feature->title}}" width="160" class="img-responsive img-rounded">
                                    @endif
                                </td>
                                <td>{{$feature->name}}</td>
                                <td>{{$feature->slug}}</td>
                                <td class="text-center">
                                    <a href="{{route('admin.features.edit',$feature->id)}}" class="btn btn-info btn-sm waves-effect">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm waves-effect" onclick="deleteFeature({{$feature->id}})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form action="{{route('admin.features.destroy',$feature->id)}}" method="POST" id="del-feature-{{$feature->id}}" style="display:none;">
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
    function deleteFeature(id) {
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
                document.getElementById('del-feature-' + id).submit();
                swal(
                    'Deleted!',
                    'Feature has been deleted.',
                    'success'
                )
            }
        })
    }
</script>
@endpush