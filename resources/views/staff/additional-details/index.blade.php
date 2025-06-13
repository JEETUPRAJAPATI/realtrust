@extends('backend.layouts.app')

@section('title', 'Additional Details')

@push('styles')
<!-- JQuery DataTable Css -->
<link rel="stylesheet" href="{{ asset('backend/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css') }}">
@endpush

@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">ADDITIONAL DETAILS LIST</h4>
                <a href="{{ route('staff.additional-details.create') }}" class="btn btn-primary">
                    CREATE
                </a>
            </div>
            <div class="card-body mt-2">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable js-exportable">
                        <thead>
                            <tr>
                                <th>SL.</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $additionalDetails as $key => $detail )
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $detail->name }}</td>
                                <td class="text-center">
                                    <a href="{{ route('staff.additional-details.edit', $detail->id) }}" class="btn btn-info btn-sm waves-effect">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm waves-effect" onclick="deleteDetail({{ $detail->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form action="{{ route('staff.additional-details.destroy', $detail->id) }}" method="POST" id="del-detail-{{ $detail->id }}" style="display:none;">
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
    function deleteDetail(id) {
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
                document.getElementById('del-detail-' + id).submit();
                swal(
                    'Deleted!',
                    'Additional Detail has been deleted.',
                    'success'
                )
            }
        })
    }
</script>
@endpush