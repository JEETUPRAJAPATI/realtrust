<html>

<head>

    <title>Laravel Signature Pad Tutorial Example - ItSolutionStuff.com </title>

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.1/css/bootstrap.css">



    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

    <link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/south-street/jquery-ui.css" rel="stylesheet">

    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <script type="text/javascript" src="http://keith-wood.name/js/jquery.signature.js"></script>



    <link rel="stylesheet" type="text/css" href="http://keith-wood.name/css/jquery.signature.css">



    <style>
        .kbw-signature {
            width: 300px;
            height: 200px;
        }

        #sig canvas {

            width: 300px;

            height: 200px;

            border: 1px solid #a1a1a1;

        }
    </style>



</head>

<body>

    <div class="container">

        <div class="row">

            <div class="col-md-10 offset-md-1 mt-5">

                <div class="card">

                    <div class="card-header">

                        <h5>Real Estate</h5>

                    </div>

                    <div class="card-body">

                        @if ($message = Session::get('success'))

                        <div class="alert alert-success  alert-dismissible">

                            <button type="button" class="close" data-dismiss="alert">×</button>

                            <strong>{{ $message }}</strong>

                        </div>

                        @endif

                        <form method="POST" action="{{ route('signature.upload') }}">

                            @csrf



                            <p>Dear Boss,

                                <br /><br />

                                I am writing to formally resign from my position as [Your Position] at [IT Company Name], with my last working day being [Last Working Day], in accordance with the notice period stipulated in my employment contract.

                                <br /><br />

                                I want to express my sincere gratitude for the opportunities and experiences I've had during my time at [IT Company Name]. It has been an incredible journey, and I have had the privilege of working alongside an outstanding team of professionals.

                                <br /><br />

                                After careful consideration, I have decided to take a new direction in my career. This decision wasn't easy, as I have cherished my time here and the projects we've accomplished together. I am immensely proud of our collective achievements.

                                <br /><br />

                                During my notice period, I am committed to ensuring a seamless transition. I am more than willing to assist in the transfer of my responsibilities, provide training to my successor, and complete any pending projects. Please let me know how I can best contribute to this process.

                                <br /><br />

                                Sincerely,

                                Hardik Savani

                            </p>



                            <div class="col-md-12">

                                <strong>Signature:</strong>

                                <br />

                                <div id="sig"></div>

                                <br />

                                <button id="clear" class="btn btn-danger btn-sm mt-1">Clear Signature</button>

                                <textarea id="signature64" name="signed" style="display: none"></textarea>

                            </div>

                            <br />

                            <div class="col-md-12 text-center">

                                <button class="btn btn-success">Submit & Download PDF</button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>



    <script type="text/javascript">
        var sig = $('#sig').signature({
            syncField: '#signature64',
            syncFormat: 'PNG'
        });



        $('#clear').click(function(e) {

            e.preventDefault();

            sig.signature('clear');

            $("#signature64").val('');

        });
    </script>



</body>

</html>