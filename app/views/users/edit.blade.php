@extends('layouts.default')

@section('pagetitle')
	<h1>Edit Information</h1>
@stop

@section('content')

	<p>Hello, {{ $user->username }}</p>

	{{ Form::model($user, array('route' => array('users.update',$user->id), 'method' => 'put')) }}

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
			{{ Form::label('password', 'Current Password') }} <br>
			{{ Form::password('password') }}
			{{ $errors->first('password') }}
		</div>

		<br>

		<div>
			{{ Form::label('newPassword', 'New Password') }} <br>
			{{ Form::password('newPassword') }}
			{{ $errors->first('newPassword') }}
		</div>


		<br>

		<div>
			{{ Form::submit('Update', ["class"=>"btn btn-primary"]) }}
		</div>

	{{ Form::close() }}

@stop