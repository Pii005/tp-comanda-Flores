<?php

class Archivos
{

    public static function leerArchivo($archivo)
    {
        $arreglo = [];
        if(file_exists($archivo))
        {
            $arreglo = explode("\n",file_get_contents($archivo));
        }
        return $arreglo;

    }

    public static function escribirCadenaHaciaArchivo($archivo,$cadena)
    {
        if(isset($cadena) and isset($archivo))
        {
            $arch = fopen($archivo,"a");
            fwrite($arch,$cadena);
            fclose($arch);
        }
    }
    public static function SubirArchivos($nombreImagen, $carpeta)
    {
        $carpetaFinal = $carpeta."/";
        $rutaFinal = "";
        $infoArchivo = "";
        $extension = "";
        if($nombreImagen != "")
        {
            $infoArchivo = pathinfo($_FILES['archivo']['name']);
            $extension = $infoArchivo['extension'];
            $rutaFinal = $carpetaFinal.$nombreImagen.".".$extension;
        }
        else
        {
            $rutaFinal = $carpetaFinal.$_FILES['archivo']['name'];
        }
        if(move_uploaded_file($_FILES['archivo']['tmp_name'],$rutaFinal))
        {
            echo "subido\n";
        }
        else
        {
            echo "Error \n";
        }
    }


}






