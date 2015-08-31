<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 13/08/2015
 * Time: 9:47
 */

namespace RM\ProductoBundle\Entity;

use League\Csv\Reader;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ZipArchive;


class CsvImagenes
{

    /** @var  UploadedFile */
    private $file;

    /** @var  string */
    private $ruta_extraccion;


    public function processFile($ruta_extraccion)
    {
        $this->setRutaExtraccion($ruta_extraccion);

        if (!$this->file instanceof UploadedFile) {
            throw new \Exception(sprintf('El archivo esta vacio'));
        }

        $zip = new ZipArchive();
        if (!$zip->open($this->file->getPathname())) {
            throw new \Exception(sprintf('No se puede subir el archivo subido'));
        }

        $zip->extractTo($this->ruta_extraccion);
        $zip->close();

        return $this->readCSV();
    }

    /**
     * Lee el fichero CSV que contiene el Id del producto y su imagen asociada.
     * Devuelve un array asociativo del tipo
     *
     * [0] =>
     *    [
     *      'id_producto' => 154,
     *      'imagen'      => C:/Symfony/Imagenes/lsdkjfsd.jpg
     *    ]
     *
     * @return array
     * @throws \Exception
     */
    public function readCSV()
    {
        $csv = $this->findCSV();

        $reader = Reader::createFromPath($csv->getPathname());
        $reader->setDelimiter(';');
        $datos = $reader->fetchAssoc(['id_producto', 'imagen']);

        return $this->findImagenesProductos($datos);
    }

    /**
     * @return \SplFileInfo|null
     * @throws \Exception
     */
    public function findCSV()
    {
        $csv = null;

        $finder = new Finder();

        $finder->files()
               ->in($this->ruta_extraccion)
               ->name('*.csv')
               ->sortByModifiedTime()
        ;

        if ($finder->count() <= 0 || $finder->count() > 1) {
            throw new \Exception('No se ha encontrado el fichero CSV');
        }

        foreach ($finder as $file) {
            $csv = $file;
        }

        return $csv;
    }

    /**
     * Intenta buscar la imagen especificada en el fichero CSV en la carpeta de las imagenes adjunta.
     * Si la encuentra, actualiza el listado de productos con la ruta. Si no, elimina la entrada.
     *
     * @param $productos
     *
     * @return mixed
     */
    public function findImagenesProductos($productos)
    {
        foreach ($productos as $index => $producto) {
            $id_producto = $producto['id_producto'];
            $imagen      = $producto['imagen'];

            $ruta = $this->ruta_extraccion . '/imagenes/' . $imagen;

            if (!file_exists($ruta) || empty($imagen) || !is_numeric($id_producto)) {
                unset($productos[$index]);
                continue;
            }

            $productos[$index]['imagen'] = $ruta;
        }

        return $productos;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadedFile $file
     *
     * @return $this
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return string
     */
    public function getRutaExtraccion()
    {
        return $this->ruta_extraccion;
    }

    /**
     * @param string $ruta
     *
     * @return $this
     * @throws \Exception
     */
    public function setRutaExtraccion($ruta = '')
    {
        if (empty($ruta)) {
            throw new \Exception('Debe introducir una ruta válida de ficheros');
        }

        if (!file_exists($ruta)) {
            mkdir($ruta, 0777, true);
        }

        $this->ruta_extraccion = $ruta;

        return $this;
    }


    public function moveImagenesProductoTo(array &$productos, $ruta = '')
    {
        if(empty($ruta) || !is_dir($ruta) || !file_exists($ruta)) {
            throw new \Exception('La ruta introducida no es válida');
        }

        foreach ($productos as $index => $producto) {
            $id_producto = $producto['id_producto'];
            $imagen      = $producto['imagen'];

            if (!is_numeric($id_producto)) {
                continue;
            }

            $info      = preg_split('/\./', basename($imagen));
            $extension = isset($info[1]) ? $info[1] : null;

            $nueva_imagen = $ruta . DIRECTORY_SEPARATOR . $id_producto . '.' . $extension;
            rename($imagen, $nueva_imagen);

            $productos[$index]['imagen'] = $nueva_imagen;

        }

        return $productos;

    }


}