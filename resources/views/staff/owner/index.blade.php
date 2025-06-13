@extends('backend.layouts.app')

@section('title', 'Owner')
@section('content')
<div class="block-header_"></div>
<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0"> OWNER LIST</h4>
                <a href="{{ route('staff.owner.create') }}" class="btn btn-primary">
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
                                <th>Verified</th>
                                <th>Mobile</th>
                                <th>Property</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $owners as $key => $owner )
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>
                                    @if($owner->image !== null)
                                    <img src="{{Storage::url('owners/'.$owner->image)}}" alt="{{$owner->title}}" width="160" class="img-responsive img-rounded">
                                    @else 
                                    <img src="{{ asset('assets/img/defaultprofile.png') }}" alt="Default Image" width="100" class="img-responsive img-rounded">
                                    @endif
                                </td>
                                <td>{{$owner->name}}</td>
                                <td>{{$owner->email}}</td>
                                <td>
                                    <form id="verification-form" action="{{ route('staff.owner.updateStatus', ['id' => $owner->id]) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="verification" id="verification-input">
                                        @if($owner->verification == 1)
                                        <span class="badge bg-success" onclick="updateVerificationStatus(0)">
                                            Verified
                                        </span>
                                        @else
                                        <span class="badge bg-danger" onclick="updateVerificationStatus(1)">
                                            Not Verified
                                        </span>
                                        @endif
                                    </form>
                                </td>
                                <td>
                                    @if($owner->mask_mobile_no)
                                    <span style="font-size: 14px; color: #1e88e5;">
                                        {{ $owner->mask_mobile_no }}
                                    </span>
                                    @else 
                                    <span style="filter: blur(4px); font-weight: bold; color: #555;">
                                        ********{{ substr($owner->mobile_no, -2) }}
                                    </span>
                                    @endif
                                </td>
                                <td><span class="badge bg-info text-dark">{{$owner->properties->count()}}</span></td>

                                <td>
                                <div class="text-center d-flex justify-content-center align-items-center">
                                    <!-- Edit Button -->
                                    <a href="{{route('staff.owner.edit', $owner->id)}}" class="btn btn-info btn-sm waves-effect" data-toggle="tooltip" data-placement="top" title="Edit Owner">
                                        <i class="bi bi-pencil-square"></i> <!-- Bootstrap Icon -->
                                    </a>

                                    <!-- Delete Button -->
                                    <button type="button" class="btn btn-danger btn-sm waves-effect" onclick="deleteUser({{ $owner->id }})" data-toggle="tooltip" data-placement="top" title="Delete Owner">
                                        <i class="bi bi-trash"></i> <!-- Bootstrap Icon -->
                                    </button>
                                    <form action="{{route('staff.owner.destroy', $owner->id)}}" method="POST" id="del-user-{{$owner->id}}" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <!-- Total Properties Button -->
                                    <a href="{{ route('staff.owner.properties', $owner->id) }}" class="btn bg-purple waves-effect" data-toggle="tooltip" data-placement="top" title="Total Properties">
                                        <i class="bi bi-house"></i> <!-- Bootstrap Icon -->
                                    </a>

                                    <!-- Verify Owner Button -->
                                    <a href="{{route('staff.owner.verify', $owner->id)}}" class="btn btn-info btn-sm waves-effect" data-toggle="tooltip" data-placement="top" title="Verify Owner">
                                        <i class="bi bi-shield-check"></i> <!-- Bootstrap Icon -->
                                    </a>
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
    document.addEventListener('DOMContentLoaded', function() {
        window.updateVerificationStatus = function(status) {
            document.getElementById('verification-input').value = status;
            document.getElementById('verification-form').submit();
        }
    });
</script>


@endpush