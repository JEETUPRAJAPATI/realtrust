@extends('backend.layouts.app')

@section('title', 'Posts')
@section('content')

<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">
            <div class="body">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="text-black mb-0">CONTACTS LIST</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover datatable js-exportable">
                            <thead>
                                <tr>
                                    <th>SL.</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile no</th>
                                    <th>Status</th>
                                    <th width="150">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach( $contacts as $key => $contact )
                                <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$contact->name}}</td>
                                    <td>{{$contact->email}}</td>
                                    <td>{{$contact->phone}}</td>
                                    <td>
                                        @if($contact->status == 'resolved')
                                        <span class="badge bg-success">Resolved</span>
                                        @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        <!-- View Contact Button -->
                                        <a href="{{route('admin.contact.show', $contact->id)}}" class="btn btn-success btn-sm waves-effect" data-toggle="tooltip" data-placement="top" title="View Contact">
                                            <i class="bi bi-eye"></i> <!-- Bootstrap Icon -->
                                        </a>

                                        <!-- Delete Contact Button -->
                                        <button type="button" class="btn btn-danger btn-sm waves-effect" onclick="deletecontact({{ $contact->id }})" data-toggle="tooltip" data-placement="top" title="Delete Contact">
                                            <i class="bi bi-trash"></i> <!-- Bootstrap Icon -->
                                        </button>

                                        <!-- Hidden Form for Deletion -->
                                        <form action="{{route('admin.contact.destroy', $contact->id)}}" method="POST" id="del-post-{{$contact->id}}" style="display:none;">
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
        function deletecontact(id) {
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