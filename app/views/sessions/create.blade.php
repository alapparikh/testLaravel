@extends('layouts.default')


@section('pagetitle')
	<center>
		<h1>Login User</h1>
	</center>
@stop

@section('content')
	
	<center>

			{{ Form::open(['route' => 'sessions.store']) }}

				<div>
					{{ Form::label('username', 'Username') }} <br>
					{{ Form::text('username') }}
					{{ $errors->first('username') }}
				</div>

				<br>

				<div>
					{{ Form::label('password', 'Password') }} <br>
					{{ Form::password('password') }}
					{{ $errors->first('password') }}
				</div>

				<br>

				<div>
					{{ Form::submit('Login', ["class"=>"btn btn-primary"]) }}
				</div>

			{{ Form::close() }}

	

	</center>

@stop