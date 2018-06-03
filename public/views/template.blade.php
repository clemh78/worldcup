<!-- Template principal -->

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
    <title>Coupe du Monde de la FIFA, Russie 2018™</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <!-- BOWER COMPONENTS -->
    <link rel="stylesheet" type="text/css" href="/bower_components/bootstrap/dist/css/bootstrap.min.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="/bower_components/angular-loading-bar/build/loading-bar.min.css"/>
    <link rel="stylesheet" href="/bower_components/fontawesome/css/font-awesome.min.css"/>

    <script src="/bower_components/jquery/dist/jquery.js" type="text/javascript"></script>
    <script src="/bower_components/angular/angular.min.js" type="text/javascript"></script>
    <script src="/bower_components/angular-cookies/angular-cookies.js" type="text/javascript"></script>
    <script src="/bower_components/angular-ui-router/release/angular-ui-router.min.js" type="text/javascript" ></script>
    <script src="/bower_components/angular-bootstrap/ui-bootstrap.min.js" type="text/javascript"></script>
    <script src="/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js" type="text/javascript"></script>
    <script src="/bower_components/angular-loading-bar/build/loading-bar.min.js" type="text/javascript"></script>
    <script src="/js/jquery.gracket.min.js"></script>

    <!-- CUSTOM -->
    <link rel="stylesheet" type="text/css" href="/css/worldcup.css?v={{Config::get('app.version')}}" media="screen" />
    <link rel="stylesheet" type="text/css" href="/css/animate.css?v={{Config::get('app.version')}}" media="screen" />
    <link rel="icon" type="image/png" href="/images/favicon.png?v={{Config::get('app.version')}}" />

    <link rel="apple-touch-icon" type="image/png" href="/images/favicon.57.png?v={{Config::get('app.version')}}"><!-- iPhone -->
    <link rel="apple-touch-icon" type="image/png" sizes="72x72" href="/images/favicon.72.png?v={{Config::get('app.version')}}"><!-- iPad -->
    <link rel="apple-touch-icon" type="image/png" sizes="114x114" href="/images/favicon.114.png?v={{Config::get('app.version')}}"><!-- iPhone4 -->
    <link rel="icon" type="image/png" href="icon.114.png?v={{Config::get('app.version')}}"><!-- Opera Speed Dial, at least 144×114 px -->
	
	<base href="{{ Config::get('app.url') }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">

    @yield('scripts')

    <script>
        REGISTER_ON = {{ (Config::get('app.register_on')==1)?1:0 }};
        VERSION = "{{Config::get('app.version')}}";
    </script>

</head>
<body @yield('body') >

<div class="guest" ng-hide="isConnected">
   <img src="/images/WCLogo.png" alt=""/>
</div>

<header ng-show="isConnected">
    <div class="navbar navbar-inverse" role="navigation" >
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <img src="/images/KUPILogo.png"/><br/>
                <div class="version" >v{{Config::get('app.version')}}</div>
            </a>
            <ul class="top-menu pull-right">
                <a href="#" ng-click="account(user)" ng-controller="usersControllerModal"><i class="fa fa-user"></i> <span class="hidden-xs">Compte</span></a>
                <a href="#" ng-click="room(user)" ng-controller="usersControllerModal"><i class="fa fa-users"></i> <span class="hidden-xs">Salons</span></a>
                <a href="#" ng-click="ranking()" ng-controller="usersControllerModal"><i class="fa fa-trophy"></i> <span class="hidden-xs">Classements</span></a>
                <a ng-show="user.isAdmin" href="#" ng-click="admin()" ng-controller="adminControllerModal" ><i class="fa fa-cog"></i> <span class="hidden-xs">Admin</span></a>
                <a href="#" ng-click="logout()"><i class="fa fa-sign-out"></i></a>
            </ul>
        </div>
    </div>
</header>

<alert class="primaryMessage" ng-controller="adminControllerPrimary" type="info" ng-show="isPrimary" id="infos" >
    <div>Le site est affiché en tant que "@@ user.login @@" mais vous êtes connecté en "@@ primary.login @@".</div>
</alert>

<alert ng-show="alerts" class="fadeInUp animated" ng-repeat="alert in alerts" type="@@ alert.class @@" close="closeAlert($index)" id="alerts">
    <div ng-show="alert.cat == 'success'">
        @@ alert.message @@
    </div>
    <div ng-show="alert.cat == 'exception'">
        @@ alert.message @@ <small>@@ alert.type @@</small><br />
        @@ alert.file @@ <small>@@ alert.line @@</small>
    </div>
    <div ng-show="alert.cat == 'error'" ng-bind-html="alert.message | unsafe"></div>
</alert>

@yield('content')


<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/locale/fr.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.11/moment-timezone-with-data.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/angular-moment/1.2.0/angular-moment.min.js" ></script>

<!-- ANGULARJS -->
<script src="/js/access.js?v={{Config::get('app.version')}}"></script>
<script src="/js/auth.js?v={{Config::get('app.version')}}"></script>
<script src="/js/services.js?v={{Config::get('app.version')}}"></script>
<script src="/js/controllers/accountsController.js?v={{Config::get('app.version')}}"></script>
<script src="/js/controllers/gamesController.js?v={{Config::get('app.version')}}"></script>
<script src="/js/controllers/usersController.js?v={{Config::get('app.version')}}"></script>
<script src="/js/controllers/betsController.js?v={{Config::get('app.version')}}"></script>
<script src="/js/controllers/transactionsController.js?v={{Config::get('app.version')}}"></script>
<script src="/js/controllers/adminController.js?v={{Config::get('app.version')}}"></script>
<script src="/js/app.js?v={{Config::get('app.version')}}"></script>


</body>
</html>
