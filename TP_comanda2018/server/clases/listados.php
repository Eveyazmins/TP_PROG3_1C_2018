<?php
include_once "pedido.php";
include_once "mesa.php";
include_once "historico.php";
include_once "empleado.php";

class listados
{
    public function traerTodosLoginEmpleados($request, $response, $args)
    {
        $todosLogin = historico::traerTodosLogin();
        for ($i=0; $i < count($todosLogin); $i++) 
        {
            $todosLogin[$i]->idEmpleado = empleado::TraerEmpleadoID($todosLogin[$i]->idEmpleado)->email;

        }
        //var_dump($todosLogin);
        return $response->withJson($todosLogin, 200);  
    }

}