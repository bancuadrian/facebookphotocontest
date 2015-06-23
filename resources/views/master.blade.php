<!DOCTYPE html>
<html lang="en" ng-app="photoContestApp">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Laravel</title>

	<link href="{{ asset('/css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/custom.css') }}" rel="stylesheet">
	<link href="{{ asset('/fonts/photofonts/styles-font1.css') }}" rel="stylesheet">

	<!-- Fonts -->
	<link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>
	<nav class="navbar navbar-default top-menu">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle Navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>

			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<li ng-class="getActive('/')"><a href="{{ url('#/') }}" translate="Menu.Home">Home</a></li>
					<li ng-class="getActive('/gallery')"><a href="{{ url('/#/gallery') }}" translate="Menu.Gallery">Gallery</a></li>
					<li ng-class="getActive('/rankings')"><a href="{{ url('/#/rankings') }}" translate="Menu.Rankings">Rankings</a></li>
					<li ng-class="getActive('/my-photo')"><a href="{{ url('/#/my-photo') }}" translate="Menu.My Photo">My Photo</a></li>
					<li ng-class="getActive('/friends-photos')"><a href="{{ url('/#/friends-photos') }}" translate="Menu.My Friends Photos">My Friends Photos</a></li>
				</ul>

				<ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="javascript:void(0)" ng-click="shareFB()" translate="Menu.Share">Share</a></li>
                            <li><a href="{{ url('/#/privacy') }}" translate="Menu.Privacy Information">Privacy Information</a></li>
                            <li><a href="{{ url('/auth/logout') }}" translate="Menu.Logout">Logout</a></li>
                        </ul>
                    </li>
				</ul>
			</div>
		</div>
	</nav>
    <div ng-view=""></div>

    <div id="fb-root"></div>

    <loading></loading>

    <script>
        var contest_name = '{{ env('CONTEST_NAME') }}';
        var app_url = '{{ env('APP_URL') }}';
    </script>
	<!-- Scripts -->
    <script src="{{ asset('/js/jquery.min.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script>

	<script src="{{ asset('/js/flow.min.js') }}"></script>

	<script src="{{ asset('/js/angular.js') }}"></script>
	<script src="{{ asset('/js/angular-route.min.js') }}"></script>
	<script src="{{ asset('/js/angular-cookies.min.js') }}"></script>
	<script src="{{ asset('/js/ngStorage.min.js') }}"></script>
	<script src="{{ asset('/js/angular-deckgrid.js') }}"></script>
	<script src="{{ asset('/js/angular-translate.min.js') }}"></script>
	<script src="{{ asset('/js/angular-translate-loader-static-files.min.js') }}"></script>
	<script src="{{ asset('/js/angular-translate-storage-cookie.min.js') }}"></script>
	<script src="{{ asset('/js/angular-translate-storage-local.min.js') }}"></script>
	<script src="{{ asset('/js/ng-flow.min.js') }}"></script>

    <script src="{{ asset('/js/app/app.js') }}"></script>
    <script src="{{ asset('/js/app/directives/photo-dialog.js') }}"></script>
    <script src="{{ asset('/js/app/directives/my-photo.js') }}"></script>
    <script src="{{ asset('/js/app/directives/photo-gallery.js') }}"></script>
    <script src="{{ asset('/js/app/directives/show-photo.js') }}"></script>
    <script src="{{ asset('/js/app/directives/loading.js') }}"></script>

    <link href="{{ asset('/css/style.css') }}" rel="stylesheet">

    <script>
        var contest_name = '{{ env('CONTEST_NAME') }}';
        window.fbAsyncInit = function() {
            FB.init({
                appId      : '{{  Config::get('services.facebook.client_id') }}',
                xfbml      : true,
                version    : 'v2.3'
            });
        };

        (function(d, s, id){
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) {return;}
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
    </script>
</body>
</html>
