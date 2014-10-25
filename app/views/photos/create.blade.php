@extends('layouts.default')

@section('pagetitle')
	<center>
		<h1>Upload photo</h1>
	</center>
@stop

@section('content')

<center>

	{{Form::open(['route' => 'photos.store','files'=>'true'])}}
	    {{ Form::label('file','File',array('id'=>'','class'=>'')) }}
  		{{ Form::file('file','',array('id'=>'','class'=>'')) }}
	    {{Form::submit('Upload!')}}
	{{Form::close()}}

</center>

@stop

