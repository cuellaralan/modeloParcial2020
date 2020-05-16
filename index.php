<?php

require_once __DIR__ .'./vendor/autoload.php';
use \Firebase\JWT\JWT;
// require './clases/paises.php';
require_once './clases/funciones.php';
require_once './clases/usuarios.php';
require_once './clases/productos.php';
require_once './clases/response.php';
require_once './clases/ventas.php';

$metodo = $_SERVER["REQUEST_METHOD"];
$path = $_SERVER['PATH_INFO'];

$key = "example_key";
// $payload = array(
    //     "iss" => "http://example.org",
    //     "aud" => "http://example.com",
    //     "iat" => 1356999524,
    //     "nbf" => 1357000000
// );

$response = new STDClass;
$response->status = 'success';

if($metodo == 'GET')
{
    $headers = getallheaders();
    //verifico token
    $token = $headers['token'];
    if(empty($_GET) && $token == '')
    {
        echo("consulta vacia realizada");
        // echo "estoy en GET"  ;
        // echo(json_encode(paises::mostrar()));
    }
    else
    {
        // var_dump($_GET);
        switch($path)
        {
            case '/detalle':
                // echo "estoy en SIGNIN <br>";
                $archivo = './files/usuarios.txt';
                if(!empty($_GET))
                {
                    // echo "POST con datos <br>";
                    if(isset($_GET['token']))
                    {
                        // echo "datos OK <br>";
                        $token = $_GET['token'];
                        $decoded = JWT::decode($token, $key, array('HS256'));
                        // print_r($decoded);
                        $respuesta = usuario::verificarUser($archivo, $decoded->name,$decoded->apellido);
                        if($respuesta === false)
                        {
                            echo "error ,los datos no coinciden con un usuario registrado";
                        }
                        else 
                        {
                            echo"Usuario: <br>";
                            print_r(json_encode($respuesta));
                            // echo "usuario no registrado";
                        }
                    }
                    else
                    {
                        echo "Token no informado";
                    }
                }
                else
                {
                    echo "Error - Datos vacíos para realizar INSERT/UPDATE";
                }
            break;
            case '/stock':
                // echo "estoy en SIGNIN <br>";
                $archivo = './files/producto.json';
                $verifica = true;
                //obtengo token
                if ($token == '') {
                    $response = new response();
                    $response->status = 'unsucces';
                    $response->data = 'error , token incorrecto';
                    echo $response;
                }
                else{
                    try {
                        //code...
                        $decoded = JWT::decode($token, $key, array('HS256'));
                        $verifica = true;
                        // print_r($decoded);
                    } catch (\Throwable $th) {
                        //throw $th;
                        $verifica = false;
                        print_r($th);
                    }
                }
                if($verifica == true)
                {
                    $listaProd = funciones::Leer($archivo);
                    $response = new response();
                    $response->status = 'succes';
                    $response->data = $listaProd;
                    print_r(json_encode($response));
                        // $respuesta = usuario::verificarUser($archivo, $decoded->name,$decoded->apellido);
                        // if($respuesta === false)
                        // {
                        //     echo "error ,los datos no coinciden con un usuario registrado";
                        // }
                        // else 
                        // {
                        //     $tipuser = $respuesta->tipo;
                        //     if($tipuser == "admin")
                        //     {
                        //         $usuarios = funciones::Listar($archivo);
                        //         foreach ($usuarios as $key => $value) {
                        //             echo json_encode($value);
                        //         }
                        //     }
                        //     else
                        //     {
                        //         echo"Usuario: <br>";
                        //         print_r(json_encode($respuesta));
                        //         // echo "usuario no registrado";
                        //     }
                        // }
                    }
                    else
                    {
                        echo "Token no informado";
                    }
            break;
            case '/ventas':
                $archivo = './files/producto.json';
                $verifica = true;
                $response = new response();
                //obtengo token
                if ($token == '') {
                    $response->status = 'unsucces';
                    $response->data = 'error , token incorrecto';
                    print_r(json_encode($response));
                }
                else{
                    try {
                        //code...
                        $decoded = JWT::decode($token, $key, array('HS256'));
                        $verifica = true;
                        // print_r($decoded);
                    } catch (\Throwable $th) {
                        //throw $th;
                        $verifica = false;
                        $response->status = 'unsucces';
                        $response->data = 'error , token incorrecto';
                        print_r(json_encode($response));
                    }
                }
                if($verifica == true)
                {
                    $datos = $decoded;
                    if ($datos->tipo != 'admin' && $datos->tipo != 'user') {
                        $response->data = 'error , tipo de usuario invalido';
                    }
                    else {
                        $respuesta = venta::traerVentas($datos->tipo, $datos->name);
                        if ($respuesta == '') {
                            $response->data = "no existen ventas";
                        }
                        else{
                            $response->status = 'succes';
                            $response->data = $respuesta;
                        }
                    }
                    print_r(json_encode($response));
                }
                break;
        }        
    }
}
else
{
    if($metodo == 'POST')
    {
        // echo "estoy en POST <br>"; //var_dump($_POST);
        // var_dump($_POST);
        switch($path)
        {
            case '/usuario':
                // echo "estoy en SIGNIN <br>";
                $archivo = './files/usuarios.json';
                if(!empty($_POST))
                {
                    // echo "POST con datos <br>";
                    if(isset($_POST['nombre'])&&isset($_POST['dni'])&&isset($_POST['obra_social'])&&isset($_POST['clave'])&&isset($_POST['tipo']))
                    {
                        // echo "datos OK <br>";
                        $nombre = $_POST['nombre'];
                        $dni = $_POST['dni'];
                        $os = $_POST['obra_social'];
                        $pass = $_POST['clave'];
                        $tipo = $_POST['tipo'];
                        // echo "usuario: $nombre apellido: $apellido , email: $email";
                        $cliente = new usuario($nombre, $dni, $os, $pass,  $tipo);
                        // echo "USER: <br>";
                        // var_dump($user);
                        // echo "<br>";
                        $respuesta = $cliente->guardarUsuario($archivo);
                        echo($respuesta);
                    }
                }
                else
                {
                    echo "Error - Datos vacíos para realizar INSERT/UPDATE";
                }
            break;
            case '/login':
                // echo "estoy en SIGNIN <br>";
                $archivo = './files/usuarios.json';
                if(!empty($_POST))
                {
                    // echo "POST con datos <br>";
                    if(isset($_POST['nombre'])&&isset($_POST['clave']))
                    {
                        // echo "datos OK <br>";
                        $nombre = $_POST['nombre'];
                        $pass = $_POST['clave'];
                        // echo "usuario: $nombre apellido: $apellido , email: $email";
                        $response = usuario::verificarLogin($archivo,$nombre,$pass);
                        $datos= $response->data;
                        print_r(json_encode($response));
                        if($response->status == 'unsucces')
                        {
                            echo "Datos erroneos, verifique.";
                        }
                        else 
                        {
                            $payload = array(
                                "iss" => "http://example.org",
                                "aud" => "http://example.com",
                                "iat" => 1356999524,
                                "nbf" => 1357000000,
                                "name" => $datos->nombre,
                                "dni" => $datos->dni,
                                "id" => $datos->id,
                                "tipo" => $datos->tipo
                            );
                            $jwt = JWT::encode($payload, $key);
                            $response->data = $jwt;
                            echo json_encode($response);
                        }
                    }
                }
                else
                {
                    echo "Error - Datos vacíos para realizar INSERT/UPDATE";
                }
            break;
            case '/stock':
                // echo "estoy en SIGNIN <br>";
                $archivo = './files/producto.json';
                $verifica = true;
                //obtengo token
                $headers = getallheaders();
                //verifico token
                $token = $headers['token'];
                if ($token == '') {
                    $response = new response();
                    $response->status = 'unsucces';
                    $response->data = 'error , token incorrecto';
                    echo $response;
                }
                else{
                    try {
                        //code...
                        $decoded = JWT::decode($token, $key, array('HS256'));
                        if ($decoded->tipo != 'admin') {
                            $verifica = false;
                        }
                        // print_r($decoded);
                    } catch (\Throwable $th) {
                        //throw $th;
                        $verifica = false;
                        print_r($th);
                    }
                }
                if(!empty($_POST) && $verifica == true)
                {
                    // echo "POST con datos <br>";
                    if(isset($_POST['producto'])&&isset($_POST['marca'])&&isset($_POST['precio'])&&isset($_POST['stock'])&&isset($_FILES['foto']))
                    {
                        // echo "datos OK <br>";
                        // var_dump($_FILES);
                        $producto = $_POST['producto'];
                        $marca = $_POST['marca'];
                        $precio = $_POST['precio'];
                        $stock = $_POST['stock'];
                        $foto = $_FILES['foto'];
                        //instancio producto
                        //respuesta
                        // $respuesta = $cliente->guardarUsuario($archivo);
                        // echo "$respuesta";
                            //obtengo path foto y guardo
                            //parametros para guardar foto
                            $fotoName = $foto['name'];
                            $path = $foto['tmp_name'];
                            $destino = './imagenes/';
                            $destiny = funciones::GuardaTemp($path, $destino, $fotoName, $producto . $marca); 
                            if($destino != $destiny)
                            {
                                $producto = new producto($producto, $marca, $precio, $stock, $destiny);
                                $response = $producto->guardarProducto($archivo);
                                echo $response;
                            }
                            else
                            {
                                $response = new response();
                                $response->status = 'unsucces';
                                $response->data = 'error al subir imagen de producto';
                                echo $response;
                            }
                    }
                }
                else
                {
                    $response = new response();
                    $response->status = 'unsucces';
                    $response->data = 'error: Usuario no permitido o datos vacíos';
                    echo json_encode($response);
                }
            break;
            case '/ventas':
                $archivo = './files/producto.json';
                $verifica = true;
                $response = new response();
                //obtengo token
                $headers = getallheaders();
                //verifico token
                $token = $headers['token'];
                // echo $token;
                if ($token == '') {
                    $response->status = 'unsucces';
                    $response->data = 'error , token incorrecto';
                    echo json_encode($response);
                }
                else{
                    try {
                        //code...
                        $decoded = JWT::decode($token, $key, array('HS256'));
                        $decoded->tipo = 'user';
                        if ($decoded->tipo != 'user') {
                            $verifica = false;
                            $response->data = 'error , tipo de usuario no permitido';
                            echo json_encode($response);
                        }
                        // echo "try decoded";
                    } catch (\Throwable $th) {
                        //throw $th;
                        $verifica = false;
                        $response->data = $th;
                        echo json_encode($response);
                    }
                }
                if(!empty($_POST) && $verifica == true)
                {              
                    if(isset($_POST['id_producto'])&&isset($_POST['cantidad'])&&isset($_POST['usuario']))
                    {
                        // echo "datos OK <br>";
                        // var_dump($_FILES);
                        $idProd = $_POST['id_producto'];
                        $cantidad = $_POST['cantidad'];
                        $usuario = $_POST['usuario'];
                        $venta = new venta($idProd, $cantidad, $usuario);
                        //verifico stock producto
                        $respuesta = funciones::BuscaEnArrayxID($archivo, $idProd);
                        // print_r(jsosn_encode($respuesta));
                        if ($respuesta->status == 'succes') {
                            $producto = $respuesta->data;
                            // echo "<br> estoy por guardar la venta <br>";
                            // print_r(json_encode($producto));
                            if ($producto->stock > $cantidad) {
                                $archivo = './files/ventas.json';
                                //guardo venta
                                $response = $venta->guardarVenta($archivo, $producto);
                                print_r($response);
                            }
                            else
                            {
                                //devuelvo JSEND informando error de stock
                                $response->data = "no hay stock de producto";
                                echo json_encode($response);    
                            }
                        }
                        // echo "<br>";
                        // print_r(json_encode($listaProd[0]));
                        // // echo "$respuesta"; 
                        //     if($destino != $destiny)
                        //     {
                        //         $producto = new producto($producto, $marca, $precio, $stock, $destiny);
                        //         $response = $producto->guardarProducto($archivo);
                        //         echo $response;
                        //     }
                    }
                }
                break;


        }
    }
    else
    {
        echo("Error 405 , method not valid");
    }
}


?>

