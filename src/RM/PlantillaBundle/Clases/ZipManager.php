<?php

namespace RM\PlantillaBundle\Clases;

class ZipManager
{

    function listar($input)
    {
        /*
         Lista de los archivos que contiene un paquete zip.
        $input: archivo zip que se va a listar
        */
        $entries = [];
        $zip = zip_open($input);
        if (!is_resource($zip)) {
            echo $this->zipFileErrMsg($zip);
            die ("No se puede leer el archivo zip. Error:" . $zip);
        } else {
            while ($entry = zip_read($zip)) {
                $entries[] = zip_entry_name($entry);
            }
        }
        zip_close($zip);
        return $entries;
    }

    function extraer($input, $destino)
    {
        /*
         Descomprime un paquete zip en un directorio especifico
        $input: archivo zip a descomprimir
        $destino: carpeta donde se descomprime
        */
        $zip = new \ZipArchive;
        if ($zip->open($input) === true) {
            $zip->extractTo($destino);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }

    //esta funcion recorre carpetas y subcarpetas
    //a�adiendo todo archivo que encuentre a su paso
    //recibe el directorio y el zip a utilizar
    function agregar_zip($dir, $zip)
    {
        //verificamos si $dir es un directorio
        if (is_dir($dir)) {
            //abrimos el directorio y lo asignamos a $da
            if ($da = opendir($dir)) {
                //leemos del directorio hasta que termine
                while (($archivo = readdir($da)) !== false) {
                    //Si es un directorio imprimimos la ruta
                    //y llamamos recursivamente esta funci�n
                    //para que verifique dentro del nuevo directorio
                    //por mas directorios o archivos
                    if (is_dir($dir . $archivo) && $archivo != "." && $archivo != "..") {
                        //echo "<strong>Creando directorio: $dir$archivo</strong><br/>";
                        agregar_zip($dir . $archivo . "/", $zip);

                        //si encuentra un archivo imprimimos la ruta donde se encuentra
                        //y agregamos el archivo al zip junto con su ruta
                    } elseif (is_file($dir . $archivo) && $archivo != "." && $archivo != "..") {
                        //echo "Agregando archivo: $dir$archivo <br/>";
                        $zip->addFile($dir . $archivo, $dir . $archivo);
                    }
                }
                //cerramos el directorio abierto en el momento
                closedir($da);
            }
        }
    } //fin de la funci�n


// 	Ejemplo llamada agregar:
// 	$rutaFinal="archivos/";

// 	$archivoZip = "Zip_dir_garabatos_linux.zip";

// 	if($zip->open($archivoZip,ZIPARCHIVE::CREATE)===true) {
// 		agregar_zip($dir, $zip);
// 		$zip->close();

// 		//Muevo el archivo a una ruta
// 		//donde no se mezcle los zip con los demas archivos
// 		@rename($archivoZip, "$rutaFinal$archivoZip");

// 		//Hasta aqui el archivo zip ya esta creado

// 		//Verifico si el archivo ha sido creado
// 		if (file_exists($rutaFinal.$archivoZip)){
// 			echo "Proceso Finalizado!! <br/><br/>
// 			Descargar: <a href='$rutaFinal$archivoZip'>$archivoZip</a>";
// 		}else{
// 		echo "Error, archivo zip no ha sido creado!!";
//         }
}