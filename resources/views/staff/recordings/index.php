<!-- resources/views/recordings/index.blade.php -->

@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Call Recordings</h2>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if (!empty($recordings['data']))
            <table class="table">
                <thead>
                    <tr>
                        <th>Call ID</th>
                        <th>Caller</th>
                        <th>Receiver</th>
                        <th>Recording Link</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recordings['data'] as $recording)
                        <tr>
                            <td>{{ $recording['call_id'] }}</td>
                            <td>{{ $recording['caller_id'] }}</td>
                            <td>{{ $recording['receiver_id'] }}</td>
                            <td>
                                <a href="{{ $recording['recording_url'] }}" target="_blank">Download</a>
                            </td>
                            <td>{{ $recording['date'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No recordings available for the selected date range.</p>
        @endif
    </div>
@endsection
