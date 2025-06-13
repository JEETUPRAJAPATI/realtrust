@extends('backend.layouts.app')

@section('title', 'Properties')

@push('styles')

<!-- JQuery DataTable Css -->
<link rel="stylesheet" href="{{ asset('backend/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css') }}">

<style>
    .btn-active {
        background-color: green;
        color: white;
    }

    .btn-inactive {
        background-color: gray;
        color: white;
    }

    .btn-reject {
        background-color: red;
        color: white;
    }

    .btn-draft {
        background-color: blue;
        color: white;
    }

    .btn-request {
        background-color: orange;
        color: white;
    }

    .btn-expired {
        background-color: darkgray;
        color: white;
    }

    .btn-delete {
        background-color: black;
        color: white;
    }
</style>
@endpush

@section('content')


<div class="row clearfix">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">PROPERTY LIST</h4>
                <a href="{{ route('staff.properties.create') }}" class="btn btn-primary">
                    CREATE
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="propertyTable" class="table datatable table-striped " style="width:100%">
                        <thead>
                            <tr>
                                <th>SL.</th>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Type</th>
                                <th>Purpose</th>
                                <th>Beds</th>
                                <th>Baths</th>
                                <th>Status</th>
                                <th>Schedule visit</th>
                                <!-- <th><i class="material-icons small">comment</i></th>
                                <th><i class="material-icons small">stars</i></th> -->
                                <th width="150">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach( $properties as $key => $property )
                            <tr>
                                <td>{{$key+1}}</td>
                                <td>

                                    @if(Storage::disk('public')->exists('property/' . $property->owner_id . '/' . $property->unique_id .'/'. $property->image))
                                    <img style="height: 143px;width: 278px;" src="{{ Storage::url('property/' . $property->owner_id . '/' . $property->unique_id .'/'. $property->image) }}" alt="{{ $property->title }}" class="img-responsive img-rounded"><br>
                                    @endif
                                </td>
                                <td>
                                    <span title="{{$property->title}}">
                                        {{ $property->title }} - {{$property->unique_id}}
                                    </span>
                                </td>
                                <td>
                                    @if($property->owner)
                                    {{ strtok($property->owner->name, " ") }}
                                    @else
                                    N/A
                                    @endif
                                </td>
                                <td>{{$property->type}}</td>
                                <td>{{$property->purpose}}</td>
                                <td>{{$property->bedroom}}</td>
                                <td>{{$property->bathroom}}</td>
                                <td>
                                    @switch($property->status)
                                    @case('Active')
                                    <span class="badge rounded-pill bg-success">{{ $property->status }}</span>
                                    @break

                                    @case('Inactive')
                                    <span class="badge rounded-pill bg-warning text-dark">{{ $property->status }}</span>
                                    @break

                                    @case('Reject')
                                    <span class="badge rounded-pill bg-danger">{{ $property->status }}</span>
                                    @break

                                    @case('Draft')
                                    <span class="badge rounded-pill bg-info text-dark">{{ $property->status }}</span>
                                    @break

                                    @case('Request')
                                    <span class="badge bg-deep-orange">{{ $property->status }}</span>
                                    @break

                                    @case('Expired')
                                    <span class="badge rounded-pill bg-warning text-dark">{{ $property->status }}</span>
                                    @break

                                    @case('Delete')
                                    <span class="badge rounded-pill bg-danger">{{ $property->status }}</span>
                                    @break

                                    @default
                                    <span class="badge rounded-pill bg-info text-dark">{{ $property->status }}</span>
                                    @endswitch
                                </td>
                                <!-- <td>
                                    <span class="badge bg-indigo">{{ $property->comments_count }}</span>
                                </td>

                                <td>
                                    @if($property->featured == true)
                                    <span class="badge bg-indigo"><i class="material-icons small">star</i></span>
                                    @endif
                                </td> -->
                                <td>
                                    @if($property->schedule_visit)
                                    @if($property->schedule_visit->status == 'sending')
                                    <span class="label bg-green">{{ $property->schedule_visit->status }}</span>
                                    <span class="label bg-brown">{{ $property->schedule_visit->timing }}</span>
                                    @else
                                    <span class="label bg-pink">{{ $property->schedule_visit->status }}</span>
                                    <span class="label bg-brown">{{ $property->schedule_visit->timing }}</span>
                                    @endif
                                    @else
                                    <span class="label bg-grey">No schedule visit</span>
                                    @endif
                                </td>

                                <td class="text-center" style="display:flex;gap: 8px;">
                                    <a href="{{route('staff.properties.show',$property->slug)}}" class="btn btn-success btn-sm waves-effect">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{route('staff.properties.edit',$property->slug)}}" class="btn btn-info btn-sm waves-effect">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm waves-effect" onclick="deleteProp({{$property->id}})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <a href="{{ route('staff.schedule_properties.view', $property->unique_id ) }}" class="btn btn-warning  waves-effect waves-float" data-toggle="tooltip" data-placement="top" title="" data-original-title="Schedule Visit">
                                        <i class="bi bi-calendar-check"></i>
                                    </a>
                                    <form action="{{route('staff.properties.destroy',$property->slug)}}" method="POST" id="del-prop-{{$property->id}}" style="display:none;">
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
    $(document).ready(function() {
        $('.dropdown-item').click(function() {
            var status = $(this).data('status');
            var propertyId = $(this).data('property-id');
            var form = $('#status-form-' + propertyId);
            var token = $('meta[name="csrf-token"]').attr('content');
            var button = $('#btn-' + propertyId);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: {
                    _method: 'PUT',
                    _token: token,
                    status: status
                },
                success: function(response) {
                    if (response.success) {
                        button.removeClass().addClass('btn btn-' + status.toLowerCase() + ' dropdown-toggle');
                        button.html(status + ' <span class="caret"></span>');
                        toastr.success('Status updated successfully!');
                    } else {
                        toastr.error('Failed to update status.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    toastr.error('An error occurred while updating status.');
                }
            });
        });
    });

    function deleteProp(id) {
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
                document.getElementById('del-prop-' + id).submit();
                swal(
                    'Deleted!',
                    'prop has been deleted.',
                    'success'
                )
            }
        })
    }
</script>


@endpush