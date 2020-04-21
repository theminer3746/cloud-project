@extends('layout.master')

@section('body')
    @if ($errors->any())
        <div class="columns">
            <div class="field column is-4 is-offset-4">
                <div class="notification is-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form action="/gateways" method="post">
        @csrf
        <div class="columns">
            <div class="field column is-4 is-offset-4">
                <label for="activation_key" class="label">Gateway Activation Key</label>
                <div class="control has-icons-left">
                    <input class="input" type="text" name="activation_key" id="activation_key" placeholder="Gateway Activation Key">
                    <span class="icon is-small is-left">
                        <i class="fas fa-key"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="columns">
            <div class="field column is-4 is-offset-4">
                <label for="gateway_name" class="label">Gateway Name</label>
                <div class="control has-icons-left">
                    <input class="input" type="text" name="gateway_name" id="gateway_name" placeholder="Gateway Name">
                    <span class="icon is-small is-left">
                        <i class="fas fa-address-card"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="columns">
            <div class="field column is-4 is-offset-4">
                <label for="smb_password" class="label">Drive Password</label>
                <div class="control has-icons-left">
                    <input class="input" type="password" name="smb_password" id="smb_password" placeholder="Drive Password">
                    <span class="icon is-small is-left">
                        <i class="fas fa-lock"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="columns">
            <div class="field column is-4 is-offset-4">
                <div class="control">
                    <input type="submit" class="button is-link" value="Create new gateway">
                </div>
            </div>
        </div>
    </form>
@endsection
