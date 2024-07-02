<?php

include_once "models\AltaComida.php";

//Ingresar POST
class PostComidas
{
    private $alta;

    public function __construct()
    {
        $this->alta = new AltaComida();
    }

    public function IngresarCmida()
    {
        if(isset($_POST["nombre"]) && isset($_POST["tipoEmpleado"]) 
        && isset($_POST["precio"]) && isset($_POST["tiempoPreparacion"]))
        {

            $this->alta->crearYGuardar($_POST["nombre"], $_POST["tipoEmpleado"], $_POST["precio"], $_POST["tiempoPreparacion"]);
            echo "Guardado";
        }
        else
        {
            echo "Error al crear la nueva comida";
        }
    }
    
    public function obtenerComida()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET["nombre"])) {
                $this->alta->mostrarComida($_GET["nombre"]);
            } else {
                echo "Error en el ingreso";
            }
        } else {
            echo "Error!!";
        }
    }

    public function eliminarComida()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') 
        {
            if (isset($_GET["nombre"])) 
            {
                $nombre = $_GET["nombre"];
                $this->alta->eliminar($nombre);
                echo "Eliminado con éxito";
            
            } 
            else 
            {
                echo "Error en el ingreso";
            }
        }
        else
        {
            echo "error!!";
        }
    }

    //MODIFICAR - ID
    //Nombre
    //TIpo cocinero
    //precio
    //tiempo preparacion

}


//Obtener pedido GET


///ELIMINAR PEDIDO DELETE







