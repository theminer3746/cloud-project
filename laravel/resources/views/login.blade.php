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

    <form action="/auth/login" method="post">
        @csrf
        <div class="columns">
            <div class="field column is-4 is-offset-4">
                <label for="username" class="label">Username</label>
                <div class="control has-icons-left">
                    <input class="input" type="text" name="username" id="username" placeholder="Username">
                    <span class="icon is-small is-left">
                        <i class="fas fa-user"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="columns">
            <div class="field column is-4 is-offset-4">
                <label for="password" class="label">Password</label>
                <div class="control has-icons-left">
                    <input class="input" type="password" name="password" id="password" placeholder="Password">
                    <span class="icon is-small is-left">
                        <i class="fas fa-key"></i>
                    </span>
                </div>
            </div>
        </div>
        <div class="columns">
            <div class="field column is-4 is-offset-4">
                <div class="control">
                    <input type="submit" class="button is-link" value="Login">
                </div>
            </div>
        </div>
    </form>
@endsection
