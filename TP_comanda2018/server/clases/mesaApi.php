<?php
include_once "mesa.php";

class mesaApi
{

    public function traerTodos($request, $response, $args) 
	{
        $todasMesas = mesa::TraerTodas();
        return $response->withJson($todasMesas, 200);  

    }
    public function traerTodosDisponibles($request, $response, $args) 
	{
        $todasMesas = mesa::TraerTodasDispobibles();
        return $response->withJson($todasMesas, 200);  

    }

    public function MesaMasUsada($request, $response, $args)
    {
        $masUsada = mesa::TraerMesaMasUtilizada();
        return $response->withJson($masUsada, 200);
    }

    public function MesaMenosUsada($request, $response, $args)
    {
        $menosUsada = mesa::TraerMesaMenosUtilizada();
        return $response->withJson($menosUsada, 200);
    }


}