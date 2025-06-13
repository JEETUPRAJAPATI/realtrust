@extends('backend.layouts.app')

@section('title', 'FieldManager')

@push('styles')

<!-- JQuery DataTable Css -->
<link rel="stylesheet" href="{{ asset('backend/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css') }}">

@endpush

@section('content')

<div class="block-header_"></div>

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="text-black mb-0">FieldManager List</h4>
                    <a href="{{ route('staff.field_manager.create') }}" class="btn btn-primary">
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
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th width="100px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach( $field_managers as $key => $field_manager )
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>
                                        @if(Storage::disk('public')->exists('field_manager/'.$field_manager->image))
                                        <img src="{{Storage::url('field_manager/'.$field_manager->image)}}" alt="{{$field_manager->title}}" width="160" class="img-responsive img-rounded">
                                        @endif
                                    </td>
                                    <td>{{$field_manager->name}}</td>
                                    <td>{{$field_manager->email}}</td>
                                    <td>{{$field_manager->mobile_no}}</td>
                                    <td>
                                        <div class="text-center d-flex justify-content-center align-items-center">
                                            <a href="{{route('staff.field_manager.edit',$field_manager->id)}}" class="btn btn-info btn-sm waves-effect">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
    
                                            <button type="button" class="btn btn-danger btn-sm waves-effect" onclick="deleteUser({{ $field_manager->id}})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form action="{{route('staff.field_manager.destroy',$field_manager->id)}}" method="POST" id="del-user-{{$field_manager->id}}" style="display:none;">
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
    <!-- Custom Js -->
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