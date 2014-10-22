@extends('layouts.default')

@section('content')
	<h1>Create New User</h1>

	{{ Form::open(['route' => 'users.store']) }}

		<div>
			{{ Form::label('username', 'Username: ') }}
			{{ Form::text('username') }}
			{{ $errors->first('username') }}
		</div>

		<div>
			{{ Form::label('email', 'Email: ') }}
			{{ Form::email('email') }}
			{{ $errors->first('email') }}
		</div>

		<div>
			{{ Form::label('password', 'Password: ') }}
			{{ Form::password('password') }}
			{{ $errors->first('password') }}
		</div>

		<div>
			{{ Form::label('confirmPassword', 'Confirm Password: ') }}
			{{ Form::password('confirmPassword') }}
			{{ $errors->first('confirmPassword') }}
		</div>

		<div>
			{{ Form::submit() }}
		</div>

	{{ Form::close() }}

@stop