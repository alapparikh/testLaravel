@extends('layouts.default')

@section('pagetitle')
	<center>
		<h1>Welcome</h1>
	</center>
@stop

@section('content')

	<li>{{ link_to_route("users.edit", "Edit your information") }}</li>
	
	@for ($i = 0; $i < count($keys); $i++)
    	{{ HTML::image($links[$keys[$i]]->link, $alt="testing", $attributes = array()) }}
	@endfor
@stop