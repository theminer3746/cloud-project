@extends('layout.master')

@section('body')
    <a href="/gateways/create">
        <button class="button is-link">
            Activate a new gateway
        </button>
    </a>

    <table class="table">
        <thead>
            <tr>
                <th>Gateway Name</th>
                <th>SMB Storage Path</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($gateways as $gateway)
                <tr>
                    <th>{{ $gateway['name'] }}</th>
                    <th>{{ $gateway['path'] }}</th>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
