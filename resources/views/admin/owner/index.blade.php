@extends('backend.layouts.app')

@section('title', 'Owner')

@push('styles')

@endpush

@section('content')

<div class="block-header_"></div>

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <div class="body">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="text-black mb-0"> OWNER LIST</h4>
                    <a href="{{ route('admin.owner.create') }}" class="btn btn-primary">
                        CREATE
                    </a>
                </div>
                <div class="card-body">
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
                                @foreach( $owners as $key => $owner )
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>
                                        @if(Storage::disk('public')->exists('owners/'.$owner->image))
                                        <img src="{{Storage::url('owners/'.$owner->image)}}" alt="{{$owner->title}}" width="100" class="img-responsive img-rounded">
                                        @else
                                        <img src="{{ asset('assets/img/defaultprofile.png') }}" alt="Default Image" width="100" class="img-responsive img-rounded">
                                        @endif
                                    </td>
                                    <td>{{$owner->name}}</td>
                                    <td>{{$owner->email}}</td>
                                    <td>{{$owner->mobile_no}}</td>
                                    <td class="text-center">
                                        <a href="{{route('admin.owner.edit',$owner->id)}}" class="btn btn-info btn-sm waves-effect">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm waves-effect" onclick="deleteUser({{ $owner->id}})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <form action="{{route('admin.owner.destroy',$owner->id)}}" method="POST" id="del-user-{{$owner->id}}" style="display:none;">
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
                    // swal(
                    //     'Deleted!',
                    //     'Owner has been deleted.',
                    //     'success'
                    // )
                }
            })
        }
    </script>


    @endpush