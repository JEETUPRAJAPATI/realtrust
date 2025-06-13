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
                <h4 class="text-black mb-0">INQUERY LIST</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable js-exportable">
                        <thead>
                            <tr>
                                <th>SL.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Society Name</th>
                                <th>Mobile no</th>
                                <th>Status</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($inquerys as $key => $inquery )
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>{{$inquery->name}}</td>
                                <td>{{$inquery->email}}</td>
                                <td>{{$inquery->phone}}</td>
                                <td>{{$inquery->society_name}}</td>
                                <td>
                                    @if($inquery->status == 'resolved')
                                    <span class="badge bg-success">Resolved</span>
                                    @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <a href="{{route('admin.inquery.show',$inquery->id)}}" class="btn btn-success btn-sm waves-effect">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm waves-effect" onclick="deleteinquery({{$inquery->id}})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form action="{{route('admin.inquery.destroy',$inquery->id)}}" method="POST" id="del-post-{{$inquery->id}}" style="display:none;">
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
    function deleteinquery(id) {
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