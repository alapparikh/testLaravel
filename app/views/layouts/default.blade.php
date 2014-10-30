<!doctype html>

<html>

	<head>
		<meta charset="utf-8">

		{{ HTML::style('/css/bootstrap.min.css') }}
		{{ HTML::style('/css/bootstrap-theme.css') }}

	</head>

	<body>

		<div id="nav">
			<div class="navbar navbar-inverse">
				<a  href="{{ URL::to('/admin') }}" class="navbar-brand">Food Alive!</a>
				@if(Auth::check())
				<button type="button" class="btn btn-default navbar-btn"><a href="{{ URL::to('/logout') }}">Logout</a></button>
				<button type="button" class="btn btn-default navbar-btn"><a href="{{ URL::to('/datadownload') }}">Download your photos</a></button>
				@else
				<button type="button" class="btn btn-default navbar-btn"><a href="{{ URL::to('/login') }}">Login</a></button>
				<button type="button" class="btn btn-default navbar-btn"><a href="{{ URL::to('/users/create') }}">Sign Up</a></button>
				@endif
			</div>
		</div>

		@if(Session::has('message'))
		<p class="alert alert-info">{{ Session::get('message') }}</p>
		@endif

		@yield('pagetitle')

		<br>

		<div class="well">

			@yield('content')

		</div>

		<!-- Javascript file -->
		<script src = "/js/boostrap.min.js"></script>

	</body>

</html>