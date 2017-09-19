<?php

namespace App\Http\Controllers;

use App\Usuario;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Traits\PersonaTrait;
use Auth;

class ConfiguracionController extends Controller{

  use PersonaTrait;

  public function primero(Request $request){

    $datosPersona = ['dni'=>$request->dni, 'nombres'=>$request->nombres, 'apellidos'=>$request->apellidos,
      'direccion'=>'', 'telefono'=>''];

    $persona = $this->guardarPersona($datosPersona);

    $usuario = new Usuario;
    $usuario->persona_dni = $persona->dni;
    $usuario->password = bcrypt($persona->dni);
    $usuario->tipo = 1;
    $usuario->foto = 'usuario.png';
    $usuario->save();

    return redirect('login')->with('correcto', 'AHORA PUEDE INGRESAR AL SISTEMA CON SU DNI');
  }

  public function administrador(){
    return view('plantillas.administrador');
  }

  public function cajero(){
    return view('plantillas.cajero');
  }

  public function editarUsuario(){
    if (Auth::user()->tipo == 1) {

      return view('usuarios.editarUsuarioAdministrador');
    } else {

      return view('usuarios.editarUsuarioCajero');
    }

  }

  public function cambiarFoto(Request $request){
    //verificar si el archivo es png o jpg
    Validator::make($request->all(), [
      'foto' => 'required|image|max:1024',
    ])->validate();

    //obtenemos el campo file definido en el formulario
    $foto = $request->file('foto');

    //obtenemos el nombre del archivo
    $nombre = time().$foto->getClientOriginalName();

    //obtenemos el usuario a editar
    $usuario = Usuario::find(Auth::user()->id);

    // verificamos que el usuario no tenga una imagen ya guardada.
    if ($usuario->foto == 'usuario.png') {

      //si la imagen es por defecto, la cambiamos.
    }else{

      //Si ya tenia una imagen personalizada, primero borramos esa imagen.
      \Storage::disk('usuarios')->delete($usuario->foto);
    }
    $usuario->foto = $nombre;
    $usuario->save();

     //indicamos que queremos guardar un nuevo archivo en el disco local
     \Storage::disk('usuarios')->put($nombre,  \File::get($foto));

     return redirect('editar-usuario')->with('correcto', 'SU FOTO FUE ACTUALIZADA.');
  }

  public function cambiarPassword(Request $request){

    Validator::make($request->all(), [
      'password1' => 'required|same:password2',
      'password2' => 'required',
    ], [
      'same'=>'LOS NUEVOS PASSWORDS NO COINCIDEN.',
    ])->validate();

    if (Hash::check($request->password, Auth::user()->password)) {

      $usuario = Usuario::find(Auth::user()->id);
      $usuario->password = Hash::make($request->password1);
      $usuario->save();

      Auth::logout();
      return redirect('login')->with('correcto', 'CAMBIÓ SU CONTRASEÑA CON SATISFACCIÓN, AHORA PUEDE INGRESAR CON SU NUEVA CONTRASEÑA');
    }
    return redirect('editar-usuario')->with('error', 'LA CONTRASEÑA DEL USUARIO ES INCORRECTA.');
  }
}
