@extends('layouts.default')

@section('pagetitle')
	<center>
		<h1>Create New User</h1>
	</center>
@stop

@section('content')
	
	<center>
		{{ Form::open(['route' => 'users.store']) }}

			<div>
				{{ Form::label('username', 'Username') }} <br>
				{{ Form::text('username') }}
				{{ $errors->first('username') }}
			</div>

			<br>

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
				{{ Form::label('confirmPassword', 'Confirm Password') }} <br>
				{{ Form::password('confirmPassword') }}
				{{ $errors->first('confirmPassword') }}
			</div>

			<div>
				{{ Form::submit('Register', ["class"=>"btn btn-primary"]) }}
			</div>

		{{ Form::close() }}

	</center>	

@stop