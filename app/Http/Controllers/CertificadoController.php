<?php

namespace App\Http\Controllers;

use App\Certificado;
use App\Tienda;
use Illuminate\Http\Request;

use Greenter\XMLSecLibs\Certificate\X509Certificate;
use Greenter\XMLSecLibs\Certificate\X509ContentType;

class CertificadoController extends Controller
{
    public function mdlSubirCertificadoDigital(Request $request)
    {
      return Tienda::find($request->id);
    }
  
    public function subirCertificadoDigital(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'certificado_digital' => 'required|file',
            'password_certificado' => 'required',
            'usuario_sunat' => 'required',
            'clave_sunat' => 'required',
            'fecha_vencimiento' => 'required'
        ]);
  
        if($validator->fails()){
            return response()->json($validator->errors()->all(), 422);
        }

        $certificate = new X509Certificate(\File::get($request->file('certificado_digital')), $request->password_certificado);
        $pem = $certificate->export(X509ContentType::PEM);
        $cer = $certificate->export(X509ContentType::CER);
        \Storage::disk('certificados')->put('certificado'.$request->id.'.pem', $pem);

        Certificado::where('tienda_id', $request->id)->delete();

        $certificado = new Certificado;
        $certificado->tienda_id = $request->id;
        $certificado->usuario_sunat = mb_strtoupper($request->usuario_sunat);
        $certificado->clave_sunat = $request->clave_sunat;
        $certificado->fecha_vencimiento = $request->fecha_vencimiento;
        $certificado->save();

        return $cer;
    }
}
