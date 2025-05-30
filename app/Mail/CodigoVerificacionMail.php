<?php

namespace App\Mail;


use Illuminate\Mail\Mailable;


class CodigoVerificacionMail extends Mailable {


    public $user;
    public $codigo;

    public function __construct($user, $codigo)
    {
        $this->user = $user;
        $this->codigo = $codigo;
    }

    public function build()
{
    return $this->subject('Código de verificación')
                ->view('emails.codigo_verificacion')
                ->with([
                    'nombreCliente' => $this->user->nombre,
                    'contraseña' => $this->codigo,
                ]);
}


}