@extends('layouts.main')


@section('content')

<form method="POST" action="do_singup">
    @csrf

    <div>
        <label for="name">Nome</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
    </div>

    <div>
        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required>
    </div>

    <div>
        <label for="password">Senha</label>
        <input id="password" type="password" name="password" required>
    </div>

    <div>
        <label for="password-confirm">Confirmar senha</label>
        <input id="password-confirm" type="password" name="password_confirmation" required>
    </div>

    <div>
        <button type="submit">
            Registrar
        </button>
    </div>
</form>


@endsection