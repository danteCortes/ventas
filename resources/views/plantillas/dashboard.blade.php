<!doctype html>
<html style="background-color:#fbba00">
  <head>
    <meta charset="UTF-8">
    <!--IE Compatibility modes-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--Mobile first-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Punto de Venta v0.1</title>

    <meta name="description" content="Sistema de punto de venta para tiendas">
    <meta name="author" content="">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="msapplication-TileColor" content="#5bc0de" />
    <meta name="msapplication-TileImage" content="{{url('assets/img/metis-tile.png')}}" />

    <!-- Bootstrap -->
    {{Html::style('assets/lib/bootstrap/css/bootstrap.css')}}

    <!-- Font Awesome -->
    {{Html::style('font-awesome/css/font-awesome.css')}}

    <!-- Metis core stylesheet -->
    {{Html::style('assets/css/main.css')}}

    <!-- metisMenu stylesheet -->
    {{Html::style('assets/lib/metismenu/metisMenu.css')}}

    <!-- onoffcanvas stylesheet -->
    {{Html::style('assets/lib/onoffcanvas/onoffcanvas.css')}}

    <!-- animate.css stylesheet -->
    {{Html::style('assets/lib/animate.css/animate.css')}}

    <style media="screen">
      .mayuscula{
        text-transform: uppercase;
      }
    </style>

    @yield('estilos')

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!--For Development Only. Not required -->
    <script>
      less = {
        env: "development",
        relativeUrls: false,
        rootpath: "/assets/"
      };
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/less.js/2.7.1/less.js"></script>

  </head>

<body class="  " style="background-color:#fbba00">
  <div class="" id="wrap" style="background-color:#fbba00">
    <div id="top" style="background-color:#fbba00">
      <!-- .navbar -->
      <nav class="navbar navbar-inverse navbar-static-top" style="background-color:#407994; border-top:#407994;">
        <div class="container-fluid">
          <!-- Brand and toggle get grouped for better mobile display -->
          <header class="navbar-header">
            <a href="#" class="navbar-brand"><img src="{{url('assets/img/logo.png')}}" alt="Brand" style="height:50px;"></a>
          </header>
          <div class="topnav">
            <div class="btn-group">
              <a data-placement="bottom" data-original-title="Pantalla Completa" data-toggle="tooltip"
                 class="btn btn-default btn-sm" id="toggleFullScreen">
                <i class="glyphicon glyphicon-fullscreen"></i>
              </a>
            </div>
            <div class="btn-group">
              @yield('alertas')
              <a data-toggle="modal" data-original-title="Ayuda" data-placement="bottom"
                 class="btn btn-default btn-sm"
                 href="#helpModal">
                  <i class="fa fa-question"></i>
              </a>
            </div>
            <div class="btn-group">
              <a href="{{url('salir')}}" data-toggle="tooltip" data-original-title="Salir" data-placement="bottom"
                class="btn btn-metis-1 btn-sm">
                <i class="fa fa-power-off"></i>
              </a>
            </div>
            <div class="btn-group">
              <a data-placement="bottom" data-original-title="Mostrar / Ocultar Menú lateral" data-toggle="tooltip"
                class="btn btn-primary btn-sm toggle-left" id="menu-toggle">
                <i class="fa fa-bars"></i>
              </a>
            </div>
          </div>
        </div>
        <!-- /.container-fluid -->
      </nav>
      <!-- /.navbar -->
      <header class="head">
        <div class="search-bar">
        </div>
        <!-- /.search-bar -->
        <div class="main-bar">
          <h3 style="color:#FFF;">
            <i class="fa fa-square-o"></i>&nbsp;
            @yield('titulo')
          </h3>
        </div>
        <!-- /.main-bar -->
      </header>
      <!-- /.head -->
    </div>
    <!-- /#top -->
    <div id="left" style="background-color:#407994">
      <div class="media user-media dker" style="background-color:#407994">
        <div class="user-media-toggleHover" style="background-color:#407994">
          <span class="fa fa-user"></span>
        </div>
          <div class="user-wrapper bg-dark" style="background-color:#407994">
            <a class="user-link" href="#">
              <img class="media-object img-thumbnail user-img" alt="User Picture" src="{{url('storage/usuarios/'.Auth::user()->foto)}}"
                style="height:64px;">
            </a>
            <div class="media-body">
              <h5 class="media-heading">{{Auth::user()->persona->nombres}}</h5>
              <ul class="list-unstyled user-info">
                <li>{{(Auth::user()->tipo == 1) ? 'Administrador' : 'Cajero - '.Auth::user()->tienda->nombre }}</li>
                <li>Editar Perfil :
                  <small><a href="{{url('editar-usuario')}}" style="color:#fff; text-decoration:none;"><i class="fa fa-edit"></i>&nbsp;editar</a></small>
                </li>
              </ul>
            </div>
          </div>
      </div>
      <!-- #menu -->
      <ul id="menu" class="">
        <li class="nav-header">Menu</li>
        <li class="nav-divider"></li>
        @yield('menu')
        <li class="nav-divider"></li>
      </ul>
      <!-- /#menu -->
    </div>
    <!-- /#left -->
    <div id="content">
      <div class="outer">
        <div class="inner bg-light lter" style="padding:10px;">
          @yield('contenido')
        </div>
        <!-- /.inner -->
      </div>
      <!-- /.outer -->
    </div>
    <!-- /#content -->
  </div>
  <!-- /#wrap -->
  <footer class="Footer dker" style="background-color:#407994">
      <p>2017 &copy; Siprom E.I.R.L</p>
  </footer>
  <!-- /#footer -->
  <!-- #helpModal -->
  <div id="helpModal" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Acerca de Siprom</h4>
        </div>
        <div class="modal-body">
          <p>
            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore
            et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
            aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
            cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in
            culpa qui officia deserunt mollit anim id est laborum.
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.modal -->
  <!-- /#helpModal -->
    <!--jQuery -->
    {{Html::script('assets/lib/jquery/jquery.js')}}

    <!--Bootstrap -->
    {{Html::script('assets/lib/bootstrap/js/bootstrap.js')}}
    <!-- MetisMenu -->
    {{Html::script('assets/lib/metismenu/metisMenu.js')}}
    <!-- onoffcanvas -->
    {{Html::script('assets/lib/onoffcanvas/onoffcanvas.js')}}
    <!-- Screenfull -->
    {{Html::script('assets/lib/screenfull/screenfull.js')}}
    <!-- Metis core scripts -->
    {{Html::script('assets/js/core.js')}}
    <!-- Metis demo scripts -->
    {{Html::script('assets/js/app.js')}}

    {{Html::script('assets/lib/mask/jquery.mask.js')}}


    @yield('scripts')
</body>

</html>
