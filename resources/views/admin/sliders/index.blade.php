@extends('backend.layouts.app')

@section('title', 'Sliders')

@push('styles')
@endpush
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="text-black mb-0">SLIDER LIST</h4>
                <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary">
                    CREATE
                </a>
            </div>
            <div class="card-body">
                <!-- Table with stripped rows -->
                <table class="table table-striped datatable" id="myTable">
                    <thead>
                        <tr>
                            <th>SL.</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th width="100px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sliders as $key => $slider)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                @if(Storage::disk('public')->exists('slider/'.$slider->image))
                                <img src="{{ Storage::url('slider/'.$slider->image) }}" alt="{{ $slider->title }}" width="160" class="img-fluid rounded">
                                @endif
                            </td>
                            <td>{{ $slider->title }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.sliders.edit', $slider->id) }}" class="btn btn-info btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteSlider({{ $slider->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <form action="{{ route('admin.sliders.destroy', $slider->id) }}" method="POST" id="del-slider-{{ $slider->id }}" style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- End Table with stripped rows -->
            </div>
        </div>
    </div>

</div>
@endsection
@push('scripts')
<script>
    function deleteSlider(id) {
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
                document.getElementById('del-slider-' + id).submit();
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