<?php
include_once "mesa.php";

class mesaApi
{

    public function traerTodos($request, $response, $args) 
	{
        $todasMesas = mesa::TraerTodas();
        return $response->withJson($todasMesas, 200);  

    }
    public function traerDisponibles($request, $response, $args) 
	{
        $todasMesas = mesa::TraerTodasDisponibles();
        if(!$todasMesas)
        {
            return $response->withJson("No hay mesas disponibles", 400);

        }
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