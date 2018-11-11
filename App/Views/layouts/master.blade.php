<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?=SITE_NAME?></title>
    <base href="<?=SITE_URL?>">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    @yield('CSSB4Main')
    <link rel="stylesheet" href="/public/_CSS/main.css" />
    @yield('CSSAfterMain')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
</head>
<body>
<header>
    <div class="container">
        <h1><?=SITE_NAME?></h1>
    </div>
</header>
<nav>
    <div class="container">
        <a href="<?=SITE_URL?>">Home</a> |
        @if (!$SESSION->IsLoggedIn())
        <a href="<?=SITE_URL?>User">Login</a>
        @else
        <a href="<?=SITE_URL?>User/Logout">Logout</a>
        @endif
    </div>
</nav>
<section class="container">
@yield('Content')
</section>
<footer>
    <div class="copyright">&copy; <?=date('Y')?> Copyright <?=SITE_NAME?></div>
</footer>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="/public/_JS/main.js"></script>
@yield('FooterScripts')
</body>
</html>
