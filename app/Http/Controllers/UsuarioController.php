<?php

namespace App\Http\Controllers;

use App\Usuario;
use App\Persona;
use Validator;
use Illuminate\Http\Request;
use App\Http\Traits\PersonaTrait;

class UsuarioController extends Controller{

  use PersonaTrait;

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(){

    return view('usuarios.inicio');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request){

    Validator::make($request->all(), [
      'dni' => 'required|digits:8',
      'nombres' => 'required|max:45',
      'apellidos' => 'required|max:45',
      'direccion' => 'max:100',
      'telefono' => 'nullable|digits:9',
      'tipo' => 'required',
      'foto' => 'nullable|image|max:1024',
    ])->validate();

    $datosPersona = ['dni'=>$request->dni, 'nombres'=>$request->nombres, 'apellidos'=>$request->apellidos,
      'direccion'=>$request->direccion, 'telefono'=>$request->telefono];

    if($persona = Persona::find($request->dni)){
      $this->actualizarPersona($persona, $datosPersona);
    }else{
      $persona = $this->guardarPersona($datosPersona);
    }

    if ($foto = $request->file('foto')) {
      $nombre_foto = time().$foto->getClientOriginalName();
      \Storage::disk('usuarios')->put($nombre_foto,  \File::get($foto));
    }else{
      $nombre_foto = 'usuario.png';
    }

    if($usuario = Usuario::where('persona_dni', $request->dni)->first()){
      $usuario->password = bcrypt($persona->dni);
      $usuario->tipo = $request->tipo;
      if ($usuario->foto != 'usuario.png') {
        \Storage::disk('usuarios')->delete($usuario->foto);
      }
      $usuario->foto = $nombre_foto;
      $usuario->save();
    }else{
      $usuario = new Usuario;
      $usuario->persona_dni = $persona->dni;
      $usuario->password = bcrypt($persona->dni);
      $usuario->tipo = $request->tipo;
      $usuario->tienda_id = $request->tienda_id;
      $usuario->foto = $nombre_foto;
      $usuario->save();
    }

    return redirect('usuario')->with('correcto', 'EL USUARIO FUE GUARDADO CON EXITO.');
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Usuario  $usuario
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Usuario $usuario){

    Validator::make($request->all(), [
      'dni' => 'required|digits:8',
      'nombres' => 'required|max:45',
      'apellidos' => 'required|max:45',
      'direccion' => 'max:100',
      'telefono' => 'nullable|digits:9',
      'tipo' => 'required',
      'foto' => 'nullable|image|max:1024',
    ])->validate();

    $datosPersona = ['dni'=>$request->dni, 'nombres'=>$request->nombres, 'apellidos'=>$request->apellidos,
      'direccion'=>$request->direccion, 'telefono'=>$request->telefono];

    $persona = $usuario->persona;
    $persona = $this->actualizarPersona($persona, $datosPersona);

    if ($foto = $request->file('foto')) {
      $nombre_foto = time().$foto->getClientOriginalName();
      \Storage::disk('usuarios')->put($nombre_foto,  \File::get($foto));
    }else{
      $nombre_foto = 'usuario.png';
    }

    $usuario->tipo = $request->tipo;
    $usuario->tienda_id = $request->tienda_id;
    // Verificamos si ya tenia una foto propia.
    if ($usuario->foto != 'usuario.png') {
      // Si ya tenia una foto propia, verificamos si esta cambiando de foto.
      if ($nombre_foto != 'usuario.png') {
        // Si le estan cambiando de foto, borramos la foto anterior.
        \Storage::disk('usuarios')->delete($usuario->foto);
        // Actualizamos el nuevo nombre de la foto.
        $usuario->foto = $nombre_foto;
      }
    }else{
      // Si no tenia una foto propia, verificamos si le estan agregando una foto.
      if ($nombre_foto != 'usuario.png') {
        // Si le estan agregando una foto, le actualizamos el nombre de la foto.
        $usuario->foto = $nombre_foto;
      }
    }
    $usuario->save();

    return redirect('usuario')->with('info', 'EL USUARIO FUE MODIFICADO CON EXITO.');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Usuario  $usuario
   * @return \Illuminate\Http\Response
   */
  public function destroy(Usuario $usuario){

    $usuario->delete();
    return redirect('usuario')->with('info', 'EL USUARIO '.$usuario->persona->nombres.' FUE ELIMINADO DE LOS REGISTROS.');
  }

  /**
   * Restaura la contraseña de un usuario a su número de DNI.
   * Fecha: 16/09/2017
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
  */
  public function restaurarContrasenia(Request $request){
    $usuario = Usuario::find($request->usuario_id);
    $usuario->password = bcrypt($usuario->persona_dni);
    $usuario->save();

    return redirect('usuario')->with('info', 'SE RESTAURO LA CONTRASEÑA DEL USUARIO '.$usuario->persona->nombres.
      ', ASEGURESE DE INFORMAR ESTE CAMBIO AL USUARIO EN CUESTIÓN.');

  }

  /**
  * Cambia el estado del atributo estado_caja a 1 para que el usuario pueda abrir su cajas e iniciar sus operaciones.
  * Fecha: 16/09/2017
  * @param \Illuminate\Http\Request  $request
  * @return \Illuminate\Http\Response
  */
  public function abrirCaja(Request $request){
    $usuario = Usuario::find($request->usuario_id);
    $usuario->estado_caja = 1;
    $usuario->save();

    return redirect('usuario')->with('info', 'SE ABRIÓ CAJA AL USUARIO '.$usuario->persona->nombres.
      ', ASEGURESE DE INFORMAR ESTA ACCIÓN AL USUARIO EN CUESTIÓN.');
  }

  /**
  * Cambia el estado del atributo estado_caja a 0 para que el usuario deje de realizar operaciones en la tienda.
  * Fecha: 16/09/2017
  * @param \Illuminate\Http\Request  $request
  * @return \Illuminate\Http\Response
  */
  public function cerrarCaja(Request $request){
    $usuario = Usuario::find($request->usuario_id);
    $usuario->estado_caja = 0;
    $usuario->save();

    return redirect('usuario')->with('info', 'SE ABRIÓ CAJA AL USUARIO '.$usuario->persona->nombres.
      ', ASEGURESE DE INFORMAR ESTA ACCIÓN AL USUARIO EN CUESTIÓN.');
  }

  /**
  * Muestra una vista de advertencia que la caja está cerrada.
  */
  public function cajaCerrada(){
    return view('usuarios.cajaCerrada');
  }
}
