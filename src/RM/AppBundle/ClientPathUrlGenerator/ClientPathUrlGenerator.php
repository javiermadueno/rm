<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 17/09/2015
 * Time: 13:28
 */

namespace RM\AppBundle\ClientPathUrlGenerator;


use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ClientPathUrlGenerator implements PathUrlGeneratorInterface
{
    /**
     * @var null|\Symfony\Component\HttpFoundation\Request
     */
    private  $request;

    /**
     * @var string
     */
    private $client;

    /**
     * @var string
     */
    private $base = 'clientes/';

    /**
     * @var array
     */
    private $nombreCarpeta = [];

    /**
     * @param TokenStorageInterface $token
     * @param RequestStack          $requestStack
     * @param array                 $path_clientes
     * @param                       $web_path
     */
    public function __construct(TokenStorageInterface $token, RequestStack $requestStack, array $path_clientes, $web_path)
    {
        $this->request = $requestStack->getMasterRequest();
        $token = $token->getToken();

        if(null === $token) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            return;
        }

        $this->client = $user->getCliente();
        $this->nombreCarpeta = $path_clientes;
        $this->web_path = $web_path;
    }

    /**
     * @param $nombre
     *
     * @return string
     * @throws \Exception
     */
    private function printPath($nombre)
    {
        if (!isset($this->nombreCarpeta[$nombre])) {
            throw new \Exception("No se ha encontrado la carpeta con nombre $nombre");
        }

        $nombre_carpeta = $this->nombreCarpeta[$nombre];

        $path = ltrim(sprintf('%s/%s', $this->client, $nombre_carpeta ));

        if(!empty($this->base)) {
            $path = trim($this->base . $path);
        }

        return $path;
    }

    /**
     * Comprueba si la ruta existe. Si no la crea.
     * @param $path
     */
    private function checkPath($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    /**
     * @param bool $absolute
     *
     * @return string
     * @throws \Exception
     */
    public function getRutaPlantillas($absolute = false)
    {
        if($absolute) {
            $ruta = $this->getAbsolutePathFor($this->printPath('plantillas'));
            $this->checkPath($ruta);
            return $ruta;
        }

        return $this->printPath('plantillas');

    }

    public function getRutaPlantilla($plantilla = null, $absolute = false)
    {
        if(!$plantilla) {
            throw new \Exception('Tiene que especificar la plantilla');
        }

        return $this->getRutaPlantillas($absolute) . $plantilla;
    }

    /**
     * @param bool $absolute
     *
     * @return string
     * @throws \Exception
     */
    public function getRutaComunicacionesGeneradas($absolute = false)
    {
        if($absolute) {
            $ruta = $this->getAbsolutePathFor($this->printPath('comunicaciones'));
            $this->checkPath($ruta);
            return $ruta;
        }

        return $this->printPath('comunicaciones');
    }

    public function getRutaComunicacionGenerada($comunicacion = null, $absolute = false)
    {
        if(!$comunicacion) {
            throw new \Exception('Tiene que especificar la comunicacion');
        }

        return $this->getRutaComunicacionesGeneradas($absolute) . $comunicacion;
    }

    /**
     * @param bool $absolute
     *
     * @return string
     * @throws \Exception
     */
    public function getRutaImagenesProducto($absolute = false)
    {
        if($absolute) {
            $ruta = $this->getAbsolutePathFor($this->printPath('productos'));
            $this->checkPath($ruta);
            return $ruta;
        }

        return $this->printPath('productos');
    }

    public function getRutaImagenProducto($producto = null, $absolute = false)
    {
        if(!$producto) {
            throw new \Exception('Tiene que especificar el producto');
        }

        return $this->getRutaImagenesProducto($absolute) . $producto;
    }

    /**
     * @param bool $absolute
     *
     * @return string
     * @throws \Exception
     */
    public function getRutaImagenesCreatividades($absolute = false)
    {
        if($absolute) {
            $ruta = $this->getAbsolutePathFor($this->printPath('creatividades'));
            $this->checkPath($ruta);
            return $ruta;
        }

        return $this->printPath('creatividades');
    }

    public function getRutaImagenCreatividad($creatividad = null, $absolute = false)
    {
        if(!$creatividad) {
            throw new \Exception('Tiene que especificar la creatividad');
        }

        return $this->getRutaImagenesCreatividades($absolute) . $creatividad;
    }

    public function getRutaBatchFor($batch = null, $absolute = false)
    {
        if(!$batch) {
            throw new \Exception('Tiene que especificar un batch');
        }

        return $this->getRutaBatch($absolute) . $batch;
    }

    /**
     * @param bool $absolute
     *
     * @return string
     * @throws \Exception
     */
    public function getRutaBatch($absolute = false)
    {
        if($absolute) {
            $ruta = $this->getAbsolutePathFor($this->printPath('batch'));
            $this->checkPath($ruta);
            return $ruta;
        }

        return $this->printPath('batch');
    }

    /**
     * @param $path
     *
     * @return string
     */
    public function getAbsoluteUrlFor($path)
    {
        $ruta =
            $this->request->getSchemeAndHttpHost() .
            $this->request->getBasePath() .
            '/' .
            $path;

        return $ruta;
    }

    /**
     * @param $path
     *
     * @return string
     */
    public function getAbsolutePathFor($path)
    {
        $path =
            $this->web_path .
            DIRECTORY_SEPARATOR .
            $path;

        return $path;
    }

    /**
     * @return string
     */
    public function getDirectorioTemporal()
    {
        $temp = sys_get_temp_dir() . '/' . $this->base . $this->client . '/' . uniqid(time());
        mkdir($temp, 0777, true);
        return $temp;


    }

} 