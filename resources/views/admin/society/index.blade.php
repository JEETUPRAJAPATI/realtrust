@extends('backend.layouts.app')

@section('title', 'FieldManager')
@section('content')

<div class="block-header_"></div>

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

       <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
    <h4 class="text-black mb-2 mb-md-0">Society List</h4>
        <div class="btn-group">
            <a href="{{ route('admin.society.create') }}" class="btn btn-primary me-2">
                Create
            </a>
            <a href="https://hashthemes.com/articles/add-google-map-with-iframe-embed-code/" 
               target="_blank" 
               class="btn btn-outline-primary">
                Google Map Embeded Steps
            </a>
        </div>
    </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable js-exportable">
                        <thead>
                            <tr>
                                <th>SL.</th>
                                <th>locality</th>
                                <th>Society</th>
                                <th>Properties Count</th>
                                <th>Map</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $societies as $key => $society )
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>{{$society->locality->name}}</td>
                                <td>{{$society->name}}</td>
                                <td>{{ $society->properties_count }} Properties</td>
                                <td>
                                    <div style=" overflow: hidden;">
                                        {!! str_replace('<iframe', '<iframe width="400" height="150"', $society->embeded_map) !!}
                                    </div>
                             </td>
                                <td>
                                    <div class="text-center d-flex justify-content-center align-items-center">
                                        <a href="{{route('admin.society.edit',$society->id)}}" class="btn btn-info btn-sm waves-effect mx-1">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm waves-effect mx-1" onclick="deleteUser({{ $society->id}})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <form action="{{route('admin.society.destroy',$society->id)}}" method="POST" id="del-user-{{$society->id}}" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
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
    function deleteUser(id) {

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
                document.getElementById('del-user-' + id).submit();
                swal(
                    'Deleted!',
                    'Slider has been deleted.',
                    'success'
                )
            }
        })
    }
</script>
@endpush