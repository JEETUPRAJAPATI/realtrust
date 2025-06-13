@extends('backend.layouts.app')

@section('title', 'Send Email')

@push('styles')

<link rel="stylesheet" href="{{asset('backend/plugins/bootstrap-select/css/bootstrap-select.css')}}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/4.4.5/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />

@endpush


@section('content')

<div class="block-header"></div>

<div class="row clearfix">
    <form action="{{ route('staff.owner.send.email') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header bg-indigo">
                    <h2>Send Email</h2>
                </div>
                <div class="body">

                    <div class="form-group form-float">
                        <label class="form-label">Name</label>
                        <div class="form-line">
                            <input type="text" name="owner_name" class="form-control" value="{{ old('owner_name', $owners->name) }}">
                        </div>
                        @error('owner_name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>


                    <div class="form-group form-float">
                        <div class="form-line">
                            <input type="date" class="form-control" name="timing" value="{{ old('timing') }}">
                        </div>
                        @error('timing')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="tinymce">Mail Body</label>
                        <textarea name="mail_body" id="tinymce"></textarea>
                        @error('mail_body')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <a href="{{route('staff.properties.index')}}" class="btn btn-danger btn-lg m-t-15 waves-effect">
                        <i class="material-icons left">arrow_back</i>
                        <span>BACK</span>
                    </a>
                    <button type="submit" class="btn btn-indigo btn-lg m-t-15 waves-effect">
                        <i class="material-icons">save</i>
                        <span>SAVE</span>
                    </button>
                </div>
            </div>
        </div>
    </form>

</div>


@endsection


@push('scripts')

<script src="{{ asset('backend/plugins/bootstrap-select/js/bootstrap-select.js') }}"></script>
<script src="{{asset('backend/plugins/tinymce/tinymce.js')}}"></script>
<!-- Bootstrap Datepicker JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="{{asset('backend/plugins/tinymce/tinymce.js')}}"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script>
    $(document).ready(function() {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd', // You can customize the format
            autoclose: true,
            todayHighlight: true
        });
    });

    $(function() {
        tinymce.init({
            selector: "textarea#tinymce",
            theme: "modern",
            height: 300,
            plugins: [
                'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker textpattern imagetools'
            ],
            toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
            toolbar2: 'print preview media | forecolor backcolor emoticons',
            image_advtab: true
        });
        tinymce.suffix = ".min";
        tinyMCE.baseURL = '{{asset('backend/plugins/tinymce')}}';
    });
</script>
@endpush