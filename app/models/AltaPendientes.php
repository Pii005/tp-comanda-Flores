<?php

include_once "./db/AccesoDatos.php"; 
include_once "./models/AltaComida.php";
include_once "./models/Pendientes.php";


class AltaPendientes
{
    

    public static function ObtenerPendientes()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pendientes");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pendientes');
    
    }

    public static function buscarPendientes($idPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pendientes WHERE id_pedido = :id_pedido");
        $consulta->bindValue(':id_pedido', $idPedido, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pendientes');
    }


    public static function mostrarPendientes($idPedido)
    {
        $pendientes = self::buscarPendientes($idPedido);
        if ($pendientes) {
            foreach ($pendientes as $pendiente) {
                $pendiente->Mostrar();
            }
        } else {
            echo "No se encontraron pendientes para el pedido con ID: $idPedido";
        }
    }

    static function buscarMenosAsignado($puesto)
    {
        $conteoEmpleados = [];

        $pendientes = self::ObtenerPendientes();
        if(count($pendientes) > 1 && $pendientes != null)
        {
            foreach ($pendientes as $pendiente) {
                if ($pendiente['puesto'] === $puesto) {
                    $idEmpleado = $pendiente['idEmpleado'];

                    if (!isset($conteoEmpleados[$idEmpleado])) {
                        $conteoEmpleados[$idEmpleado] = 0;
                    }
                    $conteoEmpleados[$idEmpleado]++;
                }
            }

            $empleadoMenosAsignado = null;
            $minAsignaciones = PHP_INT_MAX;

            foreach ($conteoEmpleados as $idEmpleado => $cantidadAsignaciones) {
                if ($cantidadAsignaciones < $minAsignaciones) {
                    $minAsignaciones = $cantidadAsignaciones;
                    $empleadoMenosAsignado = $idEmpleado;
                }
            }
            
            // echo $empleadoMenosAsignado;
            return $empleadoMenosAsignado;
        }else
        {
            $empleados = Usuario::buscarPuesto($puesto);
            if ($empleados && count($empleados) > 0) {
                foreach($empleados as $emple)
                {
                    if($emple->getPuestouser() == $puesto)
                    {
                        return $emple->getid();
                    }
                }
            }
        }
        return null;
    }



    public static function ingresoPendientes($idPedido, $items)
    {
        //reviso que las comidas esten correstas - si no es asi se cancela el pedido
        $comidasValidas = self::validarComidas($items);
        if($comidasValidas)
        {
            //mando a enviarComidas 
            echo "Enviando a guardar pendientes...<br>";
            self::enviarComidas($idPedido, $items);

            //si esta todo bien (true) cantinua con el proceso - return true 
            return true;
        }
        else
        {
            //de lo contrario cancela el pedido y tira el error para pedidos - return false
            return false;
        }   
    }

    public static function validarComidas($items)
    {
        foreach($items as $c)
        {
            if(!(AltaComida::buscarComida($c)))
            {
                echo "La comida no existe" . " - " . $c;
                return false;
            }
        }
        echo "Comidas Todas validas";
        return true;//todas las comidas estan bien ingresadas
    }

// AGREGAR que sea {Nombre = milanesa, cantidad: 1}
    public static function enviarComidas($idPedido, $items)
    {
        //Leo los items
        foreach($items as $comida)
        {
            //busco que tipo de puesto es la comida - return puesto
            $puesto = AltaComida::devolverPuesto($comida);
            echo $puesto . " - ENTRANDO A GUARDADO";
            if($puesto != null)
            {
                //busco el empleado con menos pendientes en el puesto - return IdEmpleado
                $idEmpleado = self::buscarMenosAsignado($puesto);
                // echo "id: ". $idEmpleado .  "<br>";
                
                
                if($idEmpleado != null)
                {
                    echo "Guardando pendiente... <br>";
                    $pendiente = new Pendientes($idEmpleado, $puesto, $idPedido, $comida);
                    echo $pendiente->getIdPedido();
                    //guardo en la base pendientes - guardarPendiente(PendienteCreado)
                    self::guardarPendiente($pendiente);

                }else
                {
                    throw new InvalidArgumentException("El id del empleado es nulo<br>");
                    
                }
                

            }
            else{
                throw new InvalidArgumentException("Error al guardar pendiente" . " - " . $puesto . ": la comida " . $comida);
            }
        }
    }

    public static function guardarPendiente($pendiente)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pendientes (id_empleado, puesto, id_pedido, comida, horaLlegada, Terminado) 
            VALUES (:id_empleado, :puesto, :id_pedido, :comida, :horaLlegada, :Terminado)");

            $consulta->bindValue(':id_empleado', $pendiente->getIdEmpleado(), PDO::PARAM_STR);
            $consulta->bindValue(':puesto', $pendiente->getPuesto(), PDO::PARAM_STR);
            $consulta->bindValue(':id_pedido', $pendiente->getIdPedido(), PDO::PARAM_STR);
            $consulta->bindValue(':comida', $pendiente->getComida(), PDO::PARAM_INT);
            $consulta->bindValue(':horaLlegada', $pendiente->getHoraLlegada()->format('Y-m-d H:i:s'), PDO::PARAM_STR);
            $consulta->bindValue(':Terminado', $pendiente->getTerminado(), PDO::PARAM_STR);

            $consulta->execute();
            echo "Guardado<br>";
    }

    public static function cambiarTerminado($idPedido, $comida)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDato->prepararConsulta("UPDATE pendientes SET Terminado = true WHERE id_pedido = :id_pedido AND comida = :comida");

        $consulta->bindValue(':id_pedido', $idPedido, PDO::PARAM_STR);
        $consulta->bindValue(':comida', $comida, PDO::PARAM_STR);
        $consulta->execute();

        echo "Comida cambiada a terminada con exito<br>";
    }

    

    public static function revisarPendientesTerminados($idPedido)
    {
        //$objAccesoDatos = AccesoDatos::obtenerInstancia();

        $pendientes = self::buscarPendientes($idPedido);
        $cantidadPendientes = count($pendientes);
        $terminados = 0;

        foreach($pendientes as $pendiente)
        {
            if($pendiente->getTerminado() == true)
            {
                $terminados ++;
            }
        }

        return $cantidadPendientes == $terminados;
    }

}