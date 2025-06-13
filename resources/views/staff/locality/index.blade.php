@extends('backend.layouts.app')

@section('title', 'Locality')
@section('content')

<div class="block-header_"></div>

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="header bg-indigo">
                <h2>
                    Locality List
                    <a href="{{route('staff.locality.create')}}" class="btn  btn-info waves-effect waves-light right headerightbtn">
                        <i class="material-icons left">add</i>
                        <span>CREATE </span>
                    </a>
                </h2>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                        <thead>
                            <tr>
                                <th>SL.</th>
                                <th>State</th>
                                <th>City</th>
                                <th>Locality</th>
                                <th>Properties Count</th>
                                <th width="100px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $localities as $key => $localitiy )
                            <tr>
                                <td>{{$key+1}}</td>

                                <td>{{$localitiy->state->state_name}}</td>
                                <td>{{$localitiy->city->city_name}}</td>
                                <td>{{$localitiy->name}}</td>
                                <td>{{ $localitiy->properties_count }} Properties</td>
                                <td class="text-center" style="display:flex;gap: 8px;">
                                    <a href="{{route('staff.locality.edit',$localitiy->id)}}" class="btn btn-info btn-sm waves-effect">
                                        <i class="material-icons">edit</i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm waves-effect" onclick="deleteUser({{ $localitiy->id}})">
                                        <i class="material-icons">delete</i>
                                    </button>
                                    <form action="{{route('staff.locality.destroy',$localitiy->id)}}" method="POST" id="del-user-{{$localitiy->id}}" style="display:none;">
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