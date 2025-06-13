@extends('backend.layouts.app')
@section('title', 'Show Property')
@push('styles')

@endpush
@section('content')
<?php 
use App\models\User;
use App\models\Owner;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vertical Milestone Document Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        /* Styling for vertical timeline */
        .timeline {
            position: relative;
            padding: 20px;
            border-left: 4px solid #007bff;
            margin-top: 20px;
        }

        .timeline-item {
            position: relative;
            padding-left: 30px;
            margin-bottom: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -10px;
            top: 10px;
            width: 20px;
            height: 20px;
            background: #007bff;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .timeline-item .card {
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .timeline-item .file-icon {
            font-size: 24px;
            color: #007bff;
        }
         .important-card {
          margin: 50px auto;
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .important-card .card-header {
          font-size: 1.25rem;
        }
    </style>
</head>

<body>

    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            

            <div class="card">
                <div class="card-header bg-indigo d-flex justify-content-between align-items-center">
                    <h4 class="text-black mb-0">ADD AGREEMENT</h4>
                    <a href="{{ route('staff.owner.index') }}" class="btn btn-light">
                        <i class="bi bi-arrow-left-short"></i> BACK
                    </a>
                </div>
                  <div class="card border-info shadow mt-5">
                    <div class="card-header bg-info text-white fw-bold">
                      📄 Property Document Workflow - Steps
                    </div>
                    <ul class="list-group list-group-flush">
                      <li class="list-group-item">1️⃣ <strong>Owner uploads</strong> agreement (Word or Document).</li>
                      <li class="list-group-item">2️⃣ <strong>Staff views and edits</strong> the document if needed.</li>
                      <li class="list-group-item">3️⃣ <strong>Staff sends</strong> the document to user and owner.</li>
                      <li class="list-group-item">4️⃣ <strong>User and Owner verify/modify</strong> and re-upload the document.</li>
                      <li class="list-group-item">5️⃣ <strong>Staff verifies</strong> both versions. If matched, proceed with notarization.</li>
                      <li class="list-group-item">6️⃣ <strong>Staff notarizes</strong> and sends final <span class="text-danger fw-bold">PDF Formate Only</span>.</li>
                      <li class="list-group-item">7️⃣ <strong>User and Owner sign</strong> the document and re-upload.</li>
                      <li class="list-group-item">8️⃣ <strong>Staff views</strong> the signed document and <span class="text-success fw-bold">completes the process</span>.</li>
                    </ul>
                  </div>

                
                <div class="card-body mt-2">

                    <!-- General Error Messages -->
                    @if(session('error'))
                    <div class="alert alert-danger mt-3">
                        {{ session('error') }}
                    </div>
                    @endif

                    @if(session('success'))
                    <div class="alert alert-success mt-3">
                        {{ session('success') }}
                    </div>
                    @endif
                    <form action="{{ route('staff.owner.document_uploading') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- Hidden Inputs -->
                        <input type="hidden" name="owner_id" value="{{ old('owner_id', $agreements->owner_id) }}">
                        <input type="hidden" name="property_id" value="{{ old('property_id', $agreements->properties->unique_id) }}">

                        <!-- Document Upload Field -->
                        <div class="mb-3">
                            <label for="fileInput" class="form-label">Select Document</label>
                            <input type="file" class="form-control @error('agreement') is-invalid @enderror"
                                name="agreement" id="fileInput" accept=".pdf,.doc,.docx,.jpg,.png">

                            @error('agreement')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email Input Field -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                name="email" id="email" value="{{ old('email') }}">

                            @error('email')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary mt-2 waves-effect">
                            <span>Upload</span>
                        </button>
                    </form>

                </div>

            </div>
            <div class="mt-5">
                <h4 class="">Agreement Logs</h4>
                @if($agreements->agreementLogs && $agreements->agreementLogs->count() > 0)
                @foreach($agreements->agreementLogs as $log)
                <div class="timeline-item">
                    <div class="card p-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                @if(Str::endsWith($log->agreement, ['.pdf']))
                                <i class="fas fa-file-pdf file-icon text-danger"></i>
                                @elseif(Str::endsWith($log->agreement, ['.doc', '.docx']))
                                <i class="fas fa-file-word file-icon text-primary"></i>
                                @elseif(Str::endsWith($log->agreement, ['.jpg', '.png']))
                                <i class="fas fa-file-image file-icon text-success"></i>
                                @else
                                <i class="fas fa-file-alt file-icon"></i>
                                @endif
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $log->description }}
                                    {{-- Approval Tags --}}
                                    @if($log->remark)
                                    <span class="badge bg-danger ms-2">Rejected</span>
                                    @endif
                                    @if($log->owner_approve)
                                    <span class="badge bg-success ms-2">Owner Approved</span>
                                    @endif

                                    @if($log->user_approve)
                                    <span class="badge bg-primary ms-2">User Approved</span>
                                    @endif
                                    @if($log->signature_owner)
                                    <span class="badge bg-success ms-2">Signature Adding</span>
                                    @endif

                                    @if($log->signature_user)
                                    <span class="badge bg-primary ms-2">Signature Adding</span>
                                    @endif
                                </h6>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, h:i A') }}
                                </small>
                            </div>
                            <div class="ms-auto">
                                <a href="{{ asset('storage/property/' . $log->property_id . '/agreement/' . $log->agreement)}}" target="_blank" class="btn btn-sm btn-success">View</a>
                                @if($log->owner_id)
                                <button
                                    class="btn btn-sm btn-warning ms-2 makeCall">
                                    Call Owner
                                </button>
                                @php 
                                $owner = Owner::where('id',$log->owner_id)->first();
                                @endphp
                                <input type="hidden" class="form-control" id="customerNumber"  name="customer_number" value="{{ $owner->mobile_no }}" required>
                                <input type="hidden" class="form-control" id="agentNumber" name="agent_number" value="{{ auth('staff')->user()->mobile_no }}" required>
                                @elseif($log->user_id)
                                <button
                                    class="btn btn-sm btn-warning ms-2 makeCall">
                                    Call User
                                </button>
                                @php 
                                $user = User::where('id',$log->user_id)->first();
                                @endphp
                                <input type="hidden" class="form-control" id="customerNumber"  name="customer_number" value="{{ $user->mobile_no }}" required>
                                <input type="hidden" class="form-control" id="agentNumber" name="agent_number" value="{{ auth('staff')->user()->mobile_no }}" required>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif


            </div>
        </div>
    </div>
    </div>
</body>

</html>
@endsection
@push('scripts')
<!--to make call -->
<script>
    $(document).ready(function () {
        $(".makeCall").on("click", function (e) {
            e.preventDefault();
            
            let customerNumber = $("#customerNumber").val().trim();
            let staffNumber = $("#agentNumber").val().trim();

            $.ajax({
                url: "{{ route('staff.make.call') }}", 
                type: "POST",
                data: {
                    customer_number: customerNumber,
                    staff_number: staffNumber
                },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                success: function (response) {
                               Swal.fire({
                icon: "success",
                title: "Success!",
                text: response.message || "Operation completed successfully.",
                timer: 2000,
                showConfirmButton: false
            });

                },
                error: function (xhr) {
                    let errorMessage = "Something went wrong. Please try again!";
                
                    if (xhr.responseJSON) {
                        if (typeof xhr.responseJSON.error === "string") {
                            errorMessage = xhr.responseJSON.error; // Direct string message
                        } else if (typeof xhr.responseJSON.error === "object") {
                            // Convert object errors to a readable string
                            errorMessage = Object.values(xhr.responseJSON.error).join("\n");
                        }
                    }
                
                    Swal.fire({
                        icon: "error",
                        title: "Error!",
                        text: errorMessage
                    });
                }
            });
        });
    });
</script>
<script>
        let fileCount = 0;

        function uploadFile() {
            const fileInput = document.getElementById('fileInput');
            const milestoneContainer = document.getElementById('milestoneContainer');

            if (fileInput.files.length === 0) {
                alert("Please select a file to upload.");
                return;
            }

            const file = fileInput.files[0];
            const fileURL = URL.createObjectURL(file);
            const fileType = file.name.split('.').pop().toLowerCase();

            // Set appropriate icon based on file type
            let fileIcon = '<i class="fas fa-file-alt file-icon"></i>'; // Default icon
            if (['pdf'].includes(fileType)) fileIcon = '<i class="fas fa-file-pdf file-icon text-danger"></i>';
            else if (['doc', 'docx'].includes(fileType)) fileIcon = '<i class="fas fa-file-word file-icon text-primary"></i>';
            else if (['jpg', 'png'].includes(fileType)) fileIcon = '<i class="fas fa-file-image file-icon text-success"></i>';

            fileCount++;

            // Create timeline item
            const milestone = document.createElement('div');
            milestone.classList.add('timeline-item');
            milestone.innerHTML = `
            <div class="card p-3">
                <div class="d-flex align-items-center">
                    <div class="me-3">${fileIcon}</div>
                    <div>
                        <h6 class="mb-0">${file.name.substring(0, 15)}...</h6>
                        <small class="text-muted">${new Date().toLocaleString()}</small>
                    </div>
                    <div class="ms-auto">
                        <a href="${fileURL}" target="_blank" class="btn btn-sm btn-success">View</a>
                    </div>
                </div>
            </div>
        `;

            // Add milestone to the container
            milestoneContainer.appendChild(milestone);

            // Clear file input
            fileInput.value = "";
        }
    </script>
@endpush