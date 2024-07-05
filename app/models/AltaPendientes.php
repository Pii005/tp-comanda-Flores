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

        $Pendientes =  $consulta->fetchAll(PDO::FETCH_ASSOC);

        $pendientesArray = [];

        if($Pendientes)
        {
            foreach($Pendientes as $pendiente)
            {
                $pendienteNew = new Pendientes(
                    $pendiente['idEmpleado'],
                    $pendiente['puesto'],
                    $pendiente['idPedido'],
                    $pendiente['comida'],
                );
                $pendienteNew->setHoraLlegada(new DateTime($pendiente['horaLlegada']));
                $pendienteNew->setTerminado($pendiente['terminado']);

                $pendientesArray[] = $pendienteNew;
            }
            return $pendientesArray;
        }
        return null;
    
    }

    public static function buscarPendientes($idPedido)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pendientes WHERE idPedido = :idPedido");
        $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_STR);
        $consulta->execute();
        $Pendientes =  $consulta->fetchAll(PDO::FETCH_ASSOC);

        $pendientesArray = [];

        if($Pendientes)
        {
            foreach($Pendientes as $pendiente)
            {
                $pendienteNew = new Pendientes(
                    $pendiente['idEmpleado'],
                    $pendiente['puesto'],
                    $pendiente['idPedido'],
                    $pendiente['comida'],
                );
                $pendienteNew->setHoraLlegada(new DateTime($pendiente['horaLlegada']));
                $pendienteNew->setTerminado($pendiente['terminado']);

                $pendientesArray[] = $pendienteNew;
            }
            return $pendientesArray;
        }
        return null;
    }



    public static function mostrarPendientes($idPedido)
    {
        $dataPendientes = [];
        $pendientes = self::buscarPendientes($idPedido);

        if ($pendientes != null) {
            foreach ($pendientes as $pendiente) {
                // var_dump($pendiente->getComida());
                $comidas[] = $pendiente->Mostrar();
            }
            $dataPendientes['comidas'] = $comidas; // Guarda todas las comidas en un array
            return $dataPendientes;
        } else {
            return "No se encontraron pendientes para el pedido con ID: $idPedido";
        }
    }

    static function buscarMenosAsignado($puesto)
    {
        $conteoEmpleados = [];

        // Obtener las asignaciones pendientes
        $pendientes = self::ObtenerPendientes();
        var_dump("linea: 96: "); // Verifica que los pendientes se obtienen correctamente

        // Si hay más de un pendiente
        if ($pendientes != null && count($pendientes) > 0) { // Cambiar a > 0 para incluir un solo pendiente
            foreach ($pendientes as $pendiente) {
                // Asegúrate de que muestra los valores correctos
                if ($pendiente->getPuesto() === $puesto) {
                    $idEmpleado = $pendiente->getIdEmpleado();
                    var_dump("linea: 104: ". $idEmpleado); // Verifica que el ID del empleado sea correcto

                    if (!isset($conteoEmpleados[$idEmpleado])) {
                        $conteoEmpleados[$idEmpleado] = 0;
                    }
                    $conteoEmpleados[$idEmpleado]++;
                }
            }
            var_dump("linea: 112: "); // Verifica el conteo de asignaciones
            var_dump($conteoEmpleados);
            $empleadoMenosAsignado = null;
            $minAsignaciones = PHP_INT_MAX;
            
            if(count($conteoEmpleados) != 0)
            {
                var_dump("Entro aca");
                foreach ($conteoEmpleados as $idEmpleado => $cantidadAsignaciones) {
                    if ($cantidadAsignaciones < $minAsignaciones) {
                        $minAsignaciones = $cantidadAsignaciones;
                        $empleadoMenosAsignado = $idEmpleado;
                    }
                }
                var_dump("linea: 124: ".$empleadoMenosAsignado); // Verifica el ID del empleado menos asignado
                return $empleadoMenosAsignado;
            }else
            {
                var_dump("Entro aca x2");
                return self::buscarEmpleado($puesto);
            }

            // Encontrar el empleado con el menor número de asignaciones
        } else {
            // Si no hay pendientes o solo hay uno, buscar empleados con el puesto especificado
            return self::buscarEmpleado($puesto);
        }
        return null;
    }

    static function buscarEmpleado($puesto)
    {
        $empleados = Usuario::buscarPuesto($puesto);
        // var_dump("linea: 129: ".$empleados); // Verifica que los empleados se obtienen correctamente

        if ($empleados && count($empleados) > 0) {
            var_dump("Tiene usuarios");
            foreach ($empleados as $emple) {
                var_dump($emple->getPuestouser());
                if ($emple->getPuestouser() == $puesto) {
                    var_dump("id: " . $emple->getid());
                    return $emple->getid();
                }
            }
        }
    }


    public static function validarComidas($items)
    {
        foreach($items as $c)
        {
            // var_dump(".".$c.".");
            if(!(AltaComida::buscarComida($c)))//Si me da false
            {
                return false;//La comida no es correcta
            }
        }
        return true;//todas las comidas estan bien ingresadas
    }

// AGREGAR que sea {Nombre = milanesa, cantidad: 1}
    public static function enviarComidas($idPedido, $items)
    {
        //Leo los items
        $comidasValidas = self::validarComidas($items);
        // var_dump("verificando comidas...");
        if($comidasValidas)
        {
            var_dump("comidas validas");
            try
            {
                foreach($items as $comida)
                {
                    var_dump($comida);
                    $puesto = AltaComida::devolverPuesto($comida);
                    // var_dump($puesto, $idPedido, $comida);
                    if($puesto != null)
                    {
                        $idEmpleado = self::buscarMenosAsignado($puesto);
                        var_dump("Id empleado: ".$idEmpleado);
                        if($idEmpleado != null)
                        {
                            // var_dump("Entro");
                            $pendiente = new Pendientes($idEmpleado, $puesto, $idPedido, $comida);
                            self::guardarPendiente($pendiente);
                            // throw new InvalidArgumentException("ENTRO!!!");
                        }else
                        {
                            throw new InvalidArgumentException("El id del empleado es nulo");
                            
                        }
                    }
                    else{
                        throw new InvalidArgumentException("Error al guardar pendiente" . " - " . $puesto . ": la comida " . $comida);
                    }
                }
                return true;
            }catch(Exception $e)
            {
                throw new ErrorException("error al guardar pendiente - enviarComidas - " . $e->getMessage());
            }
            
        }
        var_dump("comidas no validas...");
        return false;
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
        try
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();

            $consulta = $objAccesoDato->prepararConsulta("UPDATE pendientes SET terminado = true 
            WHERE idPedido = :idPedido AND comida = :comida");

            $consulta->bindValue(':idPedido', $idPedido, PDO::PARAM_STR);
            $consulta->bindValue(':comida', $comida, PDO::PARAM_STR);
            $consulta->execute();

            return true;

        }catch (Exception $e)
        {
            return false;
        }
    }

    
    //reviso si todas las comidas estan terminadas y retorno true o false
    public static function revisarPendientesTerminados($idPedido)
    {
        //$objAccesoDatos = AccesoDatos::obtenerInstancia();

        $pendientes = self::buscarPendientes($idPedido);
        $cantidadPendientes = count($pendientes);
        $terminados = 0;
        if($pendientes != null)
        {
            foreach($pendientes as $pendiente)
            {
                if($pendiente->getTerminado() == true)
                {
                    $terminados ++;
                }
            }

            return $cantidadPendientes == $terminados;
        }
        return false;
    }

}