<?php

class Usuario
{
    public $id;

    public $nombre;
    public $apellido;

    public $puesto;

    public $alta;

    public $baja;

    public $contraseña;
    public $email;

    public function __construct($nombre, $apellido, $puesto, $Contraseña, $email)
    {
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->puesto = $puesto;
        $this->alta = $this->obtenerTiempo();
        $this->contraseña = $Contraseña;
        $this->email = $email;
    }

    public function obtenerTiempo()
    {
        return new DateTime();
    }

    public function Mostrar()
    {
        $cadena = "<br>";
        $cadena .= "ID: " . $this->id. "<br>";
        $cadena .= "nombre: " . $this->nombre . " " . $this->apellido. "<br>";
        $cadena .= "puesto: " . $this->puesto. "<br>";
        $cadena .= "Email: " . $this->email. "<br>";
        $cadena .= "Ingreso: " . $this->alta. "<br>";
        if($this-> baja == "0000-00-00")
        {
            $cadena .= "Baja: " . $this->baja. "<br>";
        }
        return $cadena;
    }

    public static function ObtenerMostrar($email)
    {
        $empleado = self::obtenerUsuario($email);
        if($empleado)
        {
            $obj = new Usuario($empleado['nombre'], $empleado['apellido'], $empleado['puesto'], 
            $empleado['clave'], $empleado['Email']);
            $obj->setId($empleado['id']);
            $obj->setAlta($empleado['ingreso']);
            echo $obj->Mostrar();
            return true;
        }
        return false;
    }

    public static function buscarEmpleado($email)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT Email
            FROM empleados
            WHERE Email = :Email");
        $consulta->bindValue(':Email', $email, PDO::PARAM_STR);
        $consulta->execute();

        $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if (count($resultados) > 0) {
            return true;
        } else {
            return false;
        }   
    }

    public static function buscarPuesto($puesto)
{
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, apellido, puesto, ingreso, baja, clave, Email
        FROM empleados WHERE puesto = :puesto");
    $consulta->bindValue(':puesto', $puesto, PDO::PARAM_STR);
    $consulta->execute();

    // Fetch all results as associative arrays
    $resultados = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // Create an array to hold Usuario objects
    $usuarios = [];

    // Loop through each result and create a Usuario object
    foreach ($resultados as $resultado) {
        $usuario = new Usuario($resultado['nombre'], $resultado['apellido'], $resultado['puesto'], $resultado['clave'], $resultado['Email']);
        $usuario->setId($resultado['id']);
        $usuario->setAlta(new DateTime($resultado['ingreso']));

        // Optionally set the 'baja' field if needed
        if ($resultado['baja'] !== null) {
            $usuario->baja = new DateTime($resultado['baja']);
        }

        $usuarios[] = $usuario;
    }

    return $usuarios;
}

    public function crearUsuario()
    {
        
        if($this->verificarEmpleado())
        {
            echo "Verificado con exito";
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO empleados (nombre, apellido, puesto, ingreso, clave, email)
            VALUES (:nombre, :apellido, :puesto, :ingreso, :clave, :Email)");

            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
            $consulta->bindValue(':puesto', $this->puesto, PDO::PARAM_STR);
            $consulta->bindValue(':ingreso', $this->alta->format('Y-m-d H:i:s'), PDO::PARAM_STR);

            $claveHash = password_hash($this->contraseña, PASSWORD_DEFAULT);
            $consulta->bindValue(':clave', $claveHash);

            $consulta->bindValue(':Email', $this->email, PDO::PARAM_STR);
            $consulta->execute();
            echo "creado con exito";
            return true;
        }
        else
        {
            return false;
        }
        //return $objAccesoDatos->obtenerUltimoId();
    
        
    }

    public function verificarEmpleado()
    {
        if(in_array(self::getPuestouser(),
        [
            TiposEmpleados::bartender,
            TiposEmpleados::cervecero,
            TiposEmpleados::cocinero,
            TiposEmpleados::mozo,
            TiposEmpleados::socio,
            TiposEmpleados::administrador
        ]))
        {
            echo "bien";
            return true;
        }else{
            echo "no " . self::getPuestouser();
            return false;
        }
    }

    /*public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM empleados");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Empleado');
    }*/

    public static function obtenerUsuario($email)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, apellido, puesto, ingreso, baja, clave, Email
        FROM empleados WHERE Email = :Email");
        $consulta->bindValue(':Email', $email, PDO::PARAM_STR);
        $consulta->execute();
        
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    public static function verificarBaja($email)
    {   
        $user = self::obtenerUsuario($email);

        if($user)
        {
            if($user->getBaja == "0000-00-00")
            {   
                return true;
            }
            
        }
        return false;
    }


    /*public static function modificarUsuario($id, $nombre, $apellido, $puesto, $email, $contraseña) {
        $objAccesoDato = AccesoDatos::obtenerInstancia(); 
        $consulta = $objAccesoDato->prepararConsulta("UPDATE empleados SET nombre = :nombre, apellido = :apellido, puesto = :puesto, email = :email, contraseña = :contraseña WHERE id = :id");

        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $apellido, PDO::PARAM_STR);
        $consulta->bindValue(':puesto', $puesto, PDO::PARAM_STR);
        $consulta->bindValue(':email', $email, PDO::PARAM_STR);

        $claveHash = password_hash($contraseña, PASSWORD_DEFAULT);
        $consulta->bindValue(':contraseña', $claveHash, PDO::PARAM_STR);

        $consulta->bindValue(':id', $id, PDO::PARAM_INT);

        $consulta->execute();
    }*/

    public static function modificarDato($Email, $modificador, $nuevo) 
    {
        //verificar que no este de BAJA!!!
        if(self::verificarBaja($Email))
        {
            $objAccesoDato = AccesoDatos::obtenerInstancia();
        
            if ($modificador === 'clave') 
            {
                $nuevo = password_hash($nuevo, PASSWORD_DEFAULT);
            }

            if ($modificador == "puesto" && !in_array($nuevo, [
                TiposEmpleados::bartender,
                TiposEmpleados::cervecero,
                TiposEmpleados::cocinero,
                TiposEmpleados::mozo,
                TiposEmpleados::socio,
                TiposEmpleados::administrador
            ])) {
                return false;
            }

            $consulta = $objAccesoDato->prepararConsulta("UPDATE empleados SET $modificador = :nuevo WHERE Email = :Email");
            $consulta->bindValue(':nuevo', $nuevo, PDO::PARAM_STR);
            $consulta->bindValue(':Email', $Email, PDO::PARAM_INT);
            $consulta->execute();

            return true;

        }
        return false;
    }
    



    public static function borrarUsuario($email)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE empleados SET baja = :baja WHERE Email = :Email");
        $fecha = new DateTime(date("d-m-Y"));

        $consulta->bindValue(':Email', $email, PDO::PARAM_INT);
        $consulta->bindValue(':baja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
        return true;
    }

    public static function restaurarUser($email)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE empleados SET ingreso = :ingreso ,baja = :baja WHERE Email = :Email");
        $fecha = new DateTime(date("d-m-Y"));
        $baja = "0000-00-00";

        $consulta->bindValue(':Email', $email, PDO::PARAM_INT);
        $consulta->bindValue(':ingreso', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->bindValue(':baja', $baja);
        $consulta->execute();
        return true;
    }


    
    public function getNombre()
    {
        return $this->nombre;
    }

    public function getid()
    {
        return $this->id;
    }

    public function getApellido()
    {
        return $this->apellido;
    }

    public function getPuestouser()
    {
        return $this->puesto;
    }

    public function getIngreso()
    {
        return $this->alta;
    }

    public function getAlta()
    {
        return $this->alta;
    }

    public function getBaja()
    {
        return $this->baja;
    }

    public function getContraseña()
    {
        return $this->contraseña;
    }

    public function setAlta($a)
    {
        $this->alta = $a;
    }

    public function setId($a)
    {
        $this->id = $a;
    }
}