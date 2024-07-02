<?php


include_once ".\models\AltaMesa.php";

//Ingresar POST
class PostMesas
{
    public function IngresarMesas()
    {
        if(isset($_POST["id"]))
        {   
            
            $alta = new AltaMesa();
            $alta->crearYGuardar($_POST["id"]);
            
            echo "Mesa ocupada con exito";

            
        }
        else
        {
            echo "Error al ocupar mesa/s";
        }
    }
    
    public function obtenerMesa()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') 
        {
            if(isset($_GET["numero"])) {
                $alta = new AltaMesa();
                $alta->mostrarMesa($_GET["numero"]);
            } else {
                echo "Error en el ingreso: " . $_GET["numero"];
            }
        } else {
            echo "Error!!";
        }
    }

    public function eliminarMesa()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') 
        {
            if (isset($_GET["id"])) 
            {
                $alta = new AltaMesa();
                $alta->eliminarMesa($_GET["id"]);
            
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
    //estado
    //hora llegada
    //hora que se fue
    //socio q cerro
}


//Obtener pedido GET


///ELIMINAR PEDIDO DELETE







