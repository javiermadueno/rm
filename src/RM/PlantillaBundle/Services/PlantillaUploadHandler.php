<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 24/11/2015
 * Time: 9:56
 */

namespace RM\PlantillaBundle\Services;


use Mmoreram\Extractor\Extractor;
use Mmoreram\Extractor\Filesystem\SpecificDirectory;
use Mmoreram\Extractor\Resolver\ExtensionResolver;
use Psr\Log\LoggerInterface;
use RM\AppBundle\ClientPathUrlGenerator\ClientPathUrlGenerator;
use RM\PlantillaBundle\Model\Interfaces\PlantillaInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class PlantillaUploadHandler
{
    private $generator;

    private $checker;

    private $log;

    private $rutaExtraccion;

    private $carpetaPlantilla;

    private $archivosCopiados = [];

    /**
     * @param ClientPathUrlGenerator $generator
     * @param PlantillaChecker       $checker
     * @param LoggerInterface        $log
     */
    public function __construct(ClientPathUrlGenerator $generator, PlantillaChecker $checker, LoggerInterface $log)
    {
        $this->generator = $generator;
        $this->checker   = $checker;
        $this->log       = $log;
    }

    /**
     * @param PlantillaInterface $plantilla
     * @param UploadedFile       $file
     *
     * @return array
     * @throws \Exception
     */
    public function handle(PlantillaInterface $plantilla, UploadedFile $file)
    {
        $this->getRutaPlantilla($plantilla);
        $this->extract($file);

        $index   = $this->findIndexFile();
        $errores = $this->checkElementosPlantilla($plantilla, $index);

        if (!empty($errores)) {
            return $errores;
        }

        $this->parse($index);

        return [];
    }

    /**
     * @param PlantillaInterface $plantilla
     *
     * @throws \Exception
     */
    private function getRutaPlantilla(PlantillaInterface $plantilla)
    {
        $this->carpetaPlantilla = $this
            ->generator
            ->getRutaPlantilla($plantilla->getIdPlantilla());
    }

    /**
     * Extrae el archivo subido en una directorio temporal
     *
     * @param UploadedFile $file
     *
     * @return Finder
     * @throws \Mmoreram\Extractor\Exception\AdapterNotAvailableException
     * @throws \Mmoreram\Extractor\Exception\FileNotFoundException
     * @throws \Exception
     */
    private function extract(UploadedFile $file)
    {
        if (!$file->isValid()) {
            throw new \Exception("El archivo no es valido");
        }

        $this->rutaExtraccion = $this->generator->getDirectorioTemporal();
        $file                 = $file->move($this->rutaExtraccion, $file->getClientOriginalName());

        $dir       = new SpecificDirectory($this->rutaExtraccion);
        $resolver  = new ExtensionResolver();
        $extractor = new Extractor($dir, $resolver);

        /** @var Finder $files */
        $files = $extractor->extractFromFile($file);

        return $files;

    }

    /**
     *
     * @return SplFileInfo
     * @throws \Exception
     */
    private function findIndexFile()
    {
        $finder       = $this->createFinder();
        $archivosHtml = $finder
            ->files()
            ->name('*.html')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true);

        if ($archivosHtml->count() === 0) {
            throw new \Exception("Es necesario subir al menos un archivo con extension .html");
        }

        if ($archivosHtml->count() !== 1) {
            throw new \Exception("Solo se puede subir un fichero con extensión .html");
        }

        $index = null;

        /** @var SplFileInfo $plantilla_html */
        foreach ($archivosHtml->getIterator() as $plantilla_html) {
            $index = $plantilla_html;
            break;
        }

        return $index;
    }

    /**
     * @return Finder
     * @throws \Exception
     */
    private function createFinder()
    {
        if (empty($this->rutaExtraccion)) {
            throw new \Exception("No esta definida la rura de extracción");
        }

        $finder = new Finder();
        $finder->in($this->rutaExtraccion);

        return $finder;
    }

    /**
     * @param PlantillaInterface $plantilla
     * @param \SplFileInfo       $index
     *
     * @return array
     * @throws \Exception
     */
    private function checkElementosPlantilla(PlantillaInterface $plantilla, \SplFileInfo $index)
    {
        $errores = $this->checker->check($plantilla, $index->getRealPath());

        return $errores;
    }

    /**
     * @param SplFileInfo $index
     *
     * @throws \Exception
     */
    private function parse(SplFileInfo $index)
    {
        $crawler = new Crawler($index->getContents());
        $this->transFormRelativeImagesUrl($crawler);

        $index = $this->copiaACarpetaPlantilla($index, null, $name = 'index.html');

        if (!$index->isWritable()) {
            throw new \Exception("No se puede escribir en el fichero " . $index->getRealPath());
        }

        $index = $index->openFile('w');
        $index->fwrite($crawler->html());

    }

    /**
     * @param Crawler $crawler
     *
     * @throws \Exception
     */
    private function transFormRelativeImagesUrl(Crawler $crawler)
    {
        $nodosImagenes = $crawler->filter('img');

        foreach ($nodosImagenes as $img) {
            $ruta = $img->hasAttribute('src') ? $img->getAttribute('src') : null;

            if (empty($ruta) || $this->isURLAbsolute($ruta)) {
                continue;
            }

            $imagen = $this->findImage($ruta);

            $img->setAttribute('src', $this->getAbsoluteURLFor($imagen));

        }

    }

    /**
     * @param $url
     *
     * @return bool
     */
    private function  isURLAbsolute($url)
    {
        return !empty(parse_url($url, PHP_URL_SCHEME));
    }

    /**
     * @param $ruta
     *
     * @return \SplFileInfo
     * @throws \Exception
     */
    private function findImage($ruta)
    {
        if (!array_key_exists(basename($ruta), $this->archivosCopiados)) {
            $imagen = $this->copiaImagen($ruta);
        } else {
            $imagen = $this->archivosCopiados[basename($ruta)];
        }

        if (null === $imagen) {
            throw new \Exception("No se ha encontrado el archivo de imagen " . $ruta);
        }

        return $imagen;
    }

    /**
     * @param $imagen
     *
     * @return \SplFileInfo
     * @throws \Exception
     */
    private function copiaImagen($imagen)
    {
        $directorio = '/img';

        $finder = $this
            ->createFinder()
            ->name(basename($imagen));

        foreach ($finder as $archivoImagen) {
            return $this->copiaACarpetaPlantilla($archivoImagen, $directorio);
        }

        return null;
    }

    /**
     * @param \SplFileInfo $archivo
     * @param null         $directorio
     * @param null         $name
     *
     * @return \SplFileInfo
     * @throws \Exception
     */
    private function copiaACarpetaPlantilla(\SplFileInfo $archivo, $directorio = null, $name = null)
    {
        if (empty($this->carpetaPlantilla)) {
            throw new \Exception("No está definida la carpeta de destino");
        }

        $destino = $this->carpetaPlantilla;

        if (!empty($directorio)) {
            $destino .= '/' . rtrim($directorio, '/\\');
        }


        $file = new File($archivo->getRealPath());
        $file = $file->move($destino, $name);

        $archivoCopiado = new \SplFileInfo($file->getRealPath());
        $this->addArchivosCopiados($archivoCopiado);

        return $archivoCopiado;

    }

    /**
     * @param \SplFileInfo $file
     */
    private function addArchivosCopiados(\SplFileInfo $file)
    {
        $this->archivosCopiados[$file->getFilename()] = $file;
    }

    /**
     * @param \SplFileInfo $imagen
     *
     * @return string
     */
    protected function getAbsoluteURLFor(\SplFileInfo $imagen)
    {
        $path   = str_replace('\\', '/', $imagen->getRealPath());
        $neddle = '/web';
        $pos    = strpos($path, $neddle);
        $path   = false === $pos ? $path : substr($path, $pos + strlen($neddle) + 1);
        $url    = $this->generator->getAbsoluteUrlFor($path);

        return $url;
    }

    function log($mensaje)
    {
        $this->log->info($mensaje);
    }


}