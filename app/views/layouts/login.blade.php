<html>
<head>
<title>Mi proyecto
@section('title')
- Login
@show</title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">

<!-- Optional theme -->
<link rel="stylesheet" href="{{asset('css/bootstrap-theme.min.css')}}">


</head>
<body>

    <div class="container">

        <div class="row">

            <div class="col-md-12">
                @yield('content')
            </div>
        </div>

    </div>

<!-- Latest compiled and minified JavaScript -->
<script src="{{asset('js/jquery-2.1.1.min.js')}}"></script>
<script src="{{asset('js/bootstrap.min.js')}}"></script>


</body>
</html>