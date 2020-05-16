<?php

class usuario
{
    public $id;
    public $nombre;
    public $dni;
    public $obra_social;
    public $clave;
    public $tipo;

    public function __construct($name,$dni, $os, $pass, $tipe)
    {
        $this->id = $dni + 9000000;
        $this->nombre = $name;
        $this->dni = $dni;
        $this->obra_social = $os;
        $this->clave = $pass;
        $this->tipo = $tipe;
    }

    public function guardarUsuario($archivo)
    {
        $listaPersonas = funciones::Leer($archivo);
        // echo "formato: <br>";
        // print_r($listaPersonas);
        // Insertamos persona
        array_push($listaPersonas, $this);
        // print_r($listaPersonas);       
        // escribo archivo
        $retorno = funciones::Guardar($listaPersonas,$archivo,'w');
        $response = new response();
            $response->data = $this;
            $response->status = $retorno;
        return json_encode($response);
    }

    public static function verificarLogin($archivo,$name,$pass)
    {
        // echo "estoy en usuario";
        $listaUsuarios = funciones::Leer($archivo);
        /*array_search ( mixed $needle , array $haystack [, bool $strict = false ] ) : mixed
        Busca en el haystack (pajar) por la needle (aguja).*/
        $response = new response();
        foreach ($listaUsuarios as $key => $value) {
            // var_dump($value); echo "$key";
            if($value->nombre == $name && $value->clave== $pass)
            {
                $response->data = $value;
                $response->status = 'succes';
                break;
            }
        }
        return $response;
    }

    public static function verificarUser($archivo,$name,$lastname)
    {
        // echo "estoy en usuario";
        $retorno = false;
        $usuarios = funciones::Listar($archivo);
        foreach ($usuarios as $key => $value) {
            // var_dump($value); echo "$key";
            if($value->nombre == $name && $value->apellido== $lastname)
            {
                $retorno = $value;
                break;
            }
            
        }
        return $retorno;
    }

}

?>