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
				<a class="navbar-brand">SimuFood</a>
			</div>
		</div>

		@yield('pagetitle')

		<br>

		<div class="well">

			@yield('content')

		</div>

		<!-- Javascript file -->
		<script src = "/js/boostrap.min.js"></script>

	</body>

</html>