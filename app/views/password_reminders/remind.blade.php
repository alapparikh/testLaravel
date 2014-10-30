@extends('layouts.default')

@section('pagetitle')
	<h1>Password Reminder</h1>
@stop

@section('content')
	<form action="{{ route('password.reset') }}" method="POST">
	    <input type="email" name="email">
	    <input type="submit" value="Send Reminder">
	</form>
@stop