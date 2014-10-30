@extends('layout.default')

@section('pagetitle')
	<h1>Password Reminder</h1>
@stop

@section('content')
	<form action="{{ route('password.postreset') }}" method="POST">
	    <input type="hidden" name="token" value="{{ $token }}">
	    <input type="email" name="email">
	    <input type="password" name="password">
	    <input type="password" name="password_confirmation">
	    <input type="submit" value="Reset Password">
	</form>
@stop