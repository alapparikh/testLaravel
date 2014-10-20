@extends('layouts.default')

@section('content')
	<h1>Login User</h1>

	{{ Form::open(['route' => 'sessions.store']) }}

		<div>
			{{ Form::label('username', 'Username: ') }}
			{{ Form::text('username') }}
			{{ $errors->first('username') }}
		</div>

		<div>
			{{ Form::label('password', 'Password: ') }}
			{{ Form::password('password') }}
			{{ $errors->first('password') }}
		</div>

		<div>
			{{ Form::submit('Login') }}
		</div>

	{{ Form::close() }}

@stop