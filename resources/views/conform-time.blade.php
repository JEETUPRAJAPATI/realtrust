<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timming Confirmation Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


    <style>
        body {
            background-color: #f8f9fa;
        }

        .form-container {
            margin-top: 50px;
            padding: 30px;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .input-group .input-group-text {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php

    use Carbon\Carbon; ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 form-container">
                <h2 class="text-center">Confirm Your Timming</h2>
                <form action="{{ route('confirm.timing.submit') }}" method="POST" enctype="multipart/form-data">
                    @csrf <!-- Include this line if using Laravel for CSRF protection -->
                    @if(optional($visiterInfo->field_manager)->name)

                    <input type="hidden" name="property_id" value="{{ old('property_id',$visiterInfo->property_id ) }}">
                    <div class="form-group">
                        <label for="name">Owner Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', optional($visiterInfo->properties->owner)->name) }}" readonly>
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Owner Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', optional($visiterInfo->properties->owner)->email) }}" readonly>
                        @error('email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="title">Property Name</label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title', optional($visiterInfo->properties)->title) }}" readonly>
                        @error('title')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="title">Gate Pass</label></br>
                        <!-- <input type="file" class="form-control"  id="gate_pass" name="" readonly> -->
                        <input type="hidden" name="gate_pass" value="{{ $visiterInfo->gate_pass }}">
                        @if(Storage::disk('public')->exists('gate_pass/'.$visiterInfo->gate_pass) && $visiterInfo->gate_pass !== NULL )
                        <img src="{{Storage::url('gate_pass/'.$visiterInfo->gate_pass)}}" alt="Gate Pass" width="150" class="img-responsive img-rounded">

                        @else
                        <span class="text-info">No Gate Pass Available</span>
                        @endif
                        @error('gate_pass')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="title">Flat Number/Block Number</label>
                        <input type="text" class="form-control" id="flat_number" placeholder="B284/2" name="flat_number" value="{{ old('flat_number',$visiterInfo->flat_number) }}" readonly>
                        @error('flat_number')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="file_manager">Field Manager Name</label>
                        <select name="file_manager" id="file_manager" class="form-control" readonly>
                            <option value="{{ optional($visiterInfo->field_manager)->id }}">{{ optional($visiterInfo->field_manager)->name }}</option>
                        </select>
                        <input type="hidden" name="conform_timing" value="1">
                        @error('file_manager')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    @else
                    <input type="hidden" name="property_id" value="{{ old('property_id',$visiterInfo->unique_id ) }}">
                    <div class="form-group">
                        <label for="name">Owner Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', optional($visiterInfo->owner)->name) }}" readonly>
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Owner Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', optional($visiterInfo->owner)->email) }}" readonly>
                        @error('email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="title">Property Name</label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title', optional($visiterInfo)->title) }}" readonly>
                        @error('title')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="title">Gate Pass</label>
                        <input type="file" class="form-control" id="slider-image-input" name="gate_pass">
                        <img src="" id="slider-imgsrc" class="img-responsive mt-2" style="max-width: 100px;">

                        @error('gate_pass')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="title">Flat Number/Block Number<span style="color:red;"> *</span></label>
                        <input type="text" class="form-control" id="flat_number" placeholder="B284/2" name="flat_number" value="{{ old('flat_number') }}">
                        @error('flat_number')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="title">Key Person Number</label>
                        <input type="text" class="form-control" id="key_person" placeholder="Enter Number" name="key_person_number" value="{{ old('key_person_number') }}">
                        @error('key_person_number')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    @endif

                    @if(optional($visiterInfo->field_manager)->name)
                    @php

                    [$start, $end] = explode(' - ', $visiterInfo->timing);

                    // Parse both start and end using Carbon
                    $startDateTime = Carbon::createFromFormat('m/d/Y h:i A', $start);
                    $endDateTime = Carbon::createFromFormat('m/d/Y h:i A', $end);

                    // Now prepare values
                    $date = $startDateTime->format('Y-m-d'); // 2025-04-26
                    $startTime = $startDateTime->format('H:i'); // 12:23
                    $endTime = $endDateTime->format('H:i'); // 18:29

                    @endphp
                    <div class="form-group">
                        <label for="title">Select Date</label>
                        <input type="date" class="form-control" placeholder="Enter Date" name="date" value="{{ $date }}" readonly="">
                        @error('date')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group d-flex gap-4" style="gap: 15px;">
                        <div style="flex: 1;">
                            <label for="startTime">Starting Time</label>
                            <input type="time" class="form-control" placeholder="Enter Start Time" name="startTime" value="{{ $startTime }}" readonly="">
                            @error('startTime')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div style="flex: 1;">
                            <label for="endTime">End Time</label>
                            <input type="time" class="form-control" placeholder="Enter End Time" name="endTime" value="{{ $endTime }}" readonly="">
                            @error('endTime')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    @else
                    <div class="form-group">
                        <label for="title">Select Date<span style="color:red;"> *</span></label>
                        <input type="date" class="form-control" placeholder="Enter Date" name="date" value="{{ old('date') }}">
                        @error('date')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group d-flex gap-4" style="gap: 15px;">
                        <div style="flex: 1;">
                            <label for="startTime">Starting Time<span style="color:red;"> *</span></label>
                            <input type="time" class="form-control" placeholder="Enter Start Time" name="startTime" value="{{ old('startTime') }}">
                            @error('startTime')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div style="flex: 1;">
                            <label for="endTime">End Time<span style="color:red;"> *</span></label>
                            <input type="time" class="form-control" placeholder="Enter End Time" name="endTime" value="{{ old('endTime') }}">
                            @error('endTime')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    @endif

                    <button type="submit" class="btn btn-primary btn-block">Confirm Timming</button>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>




    <script>
        $(function() {
            function showImage(fileInput, imgID) {
                if (fileInput.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $(imgID).attr('src', e.target.result);
                        $(imgID).attr('alt', fileInput.files[0].name);
                    }
                    reader.readAsDataURL(fileInput.files[0]);
                }
            }
            $('#slider-image-btn').on('click', function() {
                $('#slider-image-input').click();
            });
            $('#slider-image-input').on('change', function() {
                showImage(this, '#slider-imgsrc');
            });
        })
    </script>

    <script>
        $(function() {
            $('#datetimepicker').val(''); // Start with blank

            $('#datetimepicker').daterangepicker({
                timePicker: true,
                timePicker24Hour: false,
                timePickerIncrement: 15,
                locale: {
                    format: 'MM/DD/YYYY hh:mm A',
                    separator: ' - ',
                },
                autoApply: true,
                minDate: moment(),
                isInvalidDate: function(date) {
                    return false;
                }
            }, function(start, end) {
                // Force same date for both start & end
                if (start.format('MM/DD/YYYY') !== end.format('MM/DD/YYYY')) {
                    alert("Please select the same date for both times.");
                    $('#datetimepicker').val('');
                    return;
                }

                const selectedDate = start.format('MM/DD/YYYY');
                const timeStart = start.format('hh:mm A');
                const timeEnd = end.format('hh:mm A');
                const formatted = `${selectedDate} | ${timeStart} - ${timeEnd}`;
                $('#datetimepicker').val(formatted);
            });
        });
    </script>


</body>

</html>