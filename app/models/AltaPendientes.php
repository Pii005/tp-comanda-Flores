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
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pendientes WHERE idPedido = :idPedido");
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pendientes');
    }


    public static function mostrarPendientes($idPedido)
    {
        $pendientes = self::buscarPendientes($idPedido);
        if ($pendientes) {
            foreach ($pendientes as $pendiente) {
                return $pendiente->Mostrar();
            }
        } else {
            return "No se encontraron pendientes para el pedido con ID: $idPedido";
        }
    }

    static function buscarMenosAsignado($puesto)
    {
        $conteoEmpleados = [];

        $pendientes = self::ObtenerPendientes();
        var_dump($pendientes); // Depuración: verifica los pendientes

        if ($pendientes != null && count($pendientes) > 1) {
            foreach ($pendientes as $pendiente) {
                if ($pendiente['puesto'] === $puesto) {
                    $idEmpleado = $pendiente['idEmpleado'];

                    if (!isset($conteoEmpleados[$idEmpleado])) {
                        $conteoEmpleados[$idEmpleado] = 0;
                    }
                    $conteoEmpleados[$idEmpleado]++;
                }
            }

            var_dump($conteoEmpleados); // Depuración: verifica el conteo de empleados

            $empleadoMenosAsignado = null;
            $minAsignaciones = PHP_INT_MAX;

            foreach ($conteoEmpleados as $idEmpleado => $cantidadAsignaciones) {
                if ($cantidadAsignaciones < $minAsignaciones) {
                    $minAsignaciones = $cantidadAsignaciones;
                    $empleadoMenosAsignado = $idEmpleado;
                }
            }

            var_dump($empleadoMenosAsignado); // Depuración: verifica el empleado menos asignado
            return $empleadoMenosAsignado;
        } else {
            $empleados = Usuario::buscarPuesto($puesto);
            var_dump($empleados); // Depuración: verifica los empleados

            if ($empleados && count($empleados) > 0) {
                foreach ($empleados as $emple) {
                    if ($emple->getPuestouser() == $puesto) {
                        return $emple->getid();
                    }
                }
            }
        }
        return null;
    }





    public static function ingresoPendientes($idPedido, $items)
    {
        $comidasValidas = self::validarComidas($items);
        if($comidasValidas)
        {
            
            self::enviarComidas($idPedido, $items);

            return true;
        }
        else
        {
            return false;
        }   
    }

    public static function validarComidas($items)
    {
        foreach($items as $c)
        {
            if(!(AltaComida::buscarComida($c)))
            {
                return false;
            }
        }
        return true;//todas las comidas estan bien ingresadas
    }

// AGREGAR que sea {Nombre = milanesa, cantidad: 1}
    public static function enviarComidas($idPedido, $items)
    {
        //Leo los items
        foreach($items as $comida)
        {
            $puesto = AltaComida::devolverPuesto($comida);
            // var_dump($puesto, $idPedido, $comida);
            if($puesto != null)
            {
                $idEmpleado = self::buscarMenosAsignado($puesto);
                var_dump("Cargando...");
                if($idEmpleado != null)
                {
                    var_dump("Entro");
                    // $pendiente = new Pendientes($idEmpleado, $puesto, $idPedido, $comida);
                    // self::guardarPendiente($pendiente);
                    throw new InvalidArgumentException("ENTRO!!!");

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
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pendientes (idEmpleado, puesto, idPedido, comida, horaLlegada, terminado) 
            VALUES (:idEmpleado, :puesto, :idPedido, :comida, :horaLlegada, :terminado)");

            $consulta->bindValue(':idEmpleado', $pendiente->getIdEmpleado(), PDO::PARAM_STR);
            $consulta->bindValue(':puesto', $pendiente->getPuesto(), PDO::PARAM_STR);
            $consulta->bindValue(':idPedido', $pendiente->getIdPedido(), PDO::PARAM_STR);
            $consulta->bindValue(':comida', $pendiente->getComida(), PDO::PARAM_INT);
            $consulta->bindValue(':horaLlegada', $pendiente->getHoraLlegada()->format('Y-m-d H:i:s'), PDO::PARAM_STR);
            $consulta->bindValue(':terminado', $pendiente->getTerminado(), PDO::PARAM_STR);

            $consulta->execute();
            echo "Guardado<br>";
    }

    // estado de comida terminada
    public static function cambiarTerminado($idPedido, $comida)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDato->prepararConsulta("UPDATE pendientes SET terminado = true WHERE idPedido = :idPedido AND comida = :comida");

        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_STR);
        $consulta->bindValue(':comida', $comida, PDO::PARAM_STR);
        $consulta->execute();

        echo "Comida cambiada a terminada con exito<br>";
    }

    
    //reviso si todas las comidas estan terminadas y retorno true o false
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