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
					{{ Form::label('email', 'Email') }} <br>
					{{ Form::email('email') }}
					{{ $errors->first('email') }}
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

				<br>
				
				<div>
					{{ link_to_route("password.remind", "Forgot your password again?") }}
				</div>

			{{ Form::close() }}

	

	</center>

@stop