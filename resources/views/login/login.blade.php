<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!--IE Compatibility modes-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--Mobile first-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Ingresar</title>

    <meta name="description" content="Ingreso al sistema punto de venta v1.0">
    <meta name="author" content="Siprom E.I.R.L.">

    <meta name="msapplication-TileColor" content="#5bc0de" />
    <meta name="msapplication-TileImage" content="assets/img/metis-tile.png" />

    <!-- Bootstrap -->
    <link rel="stylesheet" href="assets/lib/bootstrap/css/bootstrap.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="assets/lib/font-awesome/css/font-awesome.css">

    <!-- Metis core stylesheet -->
    <link rel="stylesheet" href="assets/css/main.css">

    <!-- metisMenu stylesheet -->
    <link rel="stylesheet" href="assets/lib/metismenu/metisMenu.css">

    <!-- onoffcanvas stylesheet -->
    <link rel="stylesheet" href="assets/lib/onoffcanvas/onoffcanvas.css">

    <!-- animate.css stylesheet -->
    <link rel="stylesheet" href="assets/lib/animate.css/animate.css">

    <style media="screen">
      .mayuscula{
        text-transform: uppercase;
      }
    </style>


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body class="login">
  <div class="row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
      @if(Session::has('correcto'))
        <div class="alert alert-success alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <strong>Correcto!</strong> {{Session::get('correcto')}}
        </div>
      @elseif(Session::has('info'))
        <div class="alert alert-info alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <strong>Correcto!</strong> {{Session::get('info')}}
        </div>
      @elseif(Session::has('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <strong>Error!</strong> {{Session::get('error')}}
        </div>
      @endif
      @foreach($errors->all() as $mensaje)
      <div class="alert alert-info alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Ups!</strong> {{$mensaje}}
      </div>
      @endforeach
    </div>
  </div>
  <div class="form-signin">
    <div class="text-center">
      <img src="assets/img/logo.png" alt="Siprom" style="width:200px;">
    </div>
    <hr>
    <div class="tab-content">
      <div id="login" class="tab-pane active">
        {{Form::open(['url'=>'ingresar'])}}
          <p class="text-muted text-center">
            Ingrese su DNI y password
          </p>
          <input type="text" name="dni" placeholder="DNI" class="form-control top" required>
          <input type="password" name="password" placeholder="PASSWORD" class="form-control bottom" required>
          <div class="checkbox">
      		  <label>
      		    <input type="checkbox" name="recordarme"> Recordarme
      		  </label>
      		</div>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Ingresar</button>
        {{Form::close()}}
      </div>
      <!-- Formularios para recuperar passwors y registrarse, solo para pruebas de SIPROM
        <div id="forgot" class="tab-pane">
          <form action="index.html">
            <p class="text-muted text-center">Ingrese su Correo Electrónico</p>
            <input type="email" placeholder="correo@dominio.com" class="form-control">
            <br>
            <button class="btn btn-lg btn-danger btn-block" type="submit">Recuperar Password</button>
          </form>
        </div>
      -->
      <div id="signup" class="tab-pane">
        {{Form::open(['url'=>'primer-usuario'])}}
          <input type="text" name="dni" placeholder="DNI" class="form-control top">
          <input type="text" name="nombres" placeholder="NOMBRES" class="form-control middle mayuscula">
          <input type="text" name="apellidos" placeholder="APELLIDOS" class="form-control bottom mayuscula">
          <button class="btn btn-lg btn-success btn-block" type="submit">Registrar</button>
        {{Form::close()}}
      </div>
    </div>
    @if(!\App\Usuario::first())
    <hr>
    <div class="text-center">
      <ul class="list-inline">
        <li><a class="text-muted" href="#login" data-toggle="tab">Ingresar</a></li>
        <!-- Botones para pruebas de SIPROM, recuperar password y registrarse.
        <li><a class="text-muted" href="#forgot" data-toggle="tab">Recuperar Password</a></li>
      -->
        <li><a class="text-muted" href="#signup" data-toggle="tab">Registrarse</a></li>
      </ul>
    </div>
    @endif
  </div>


    <!--jQuery -->
    <script src="assets/lib/jquery/jquery.js"></script>

    <!--Bootstrap -->
    <script src="assets/lib/bootstrap/js/bootstrap.js"></script>


    <script type="text/javascript">
        (function($) {
            $(document).ready(function() {
                $('.list-inline li > a').click(function() {
                    var activeForm = $(this).attr('href') + ' > form';
                    //console.log(activeForm);
                    $(activeForm).addClass('animated fadeIn');
                    //set timer to 1 seconds, after that, unload the animate animation
                    setTimeout(function() {
                        $(activeForm).removeClass('animated fadeIn');
                    }, 1000);
                });
            });
        })(jQuery);
    </script>
</body>

</html>
