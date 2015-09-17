<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 05/03/2015
 * Time: 16:45
 */

namespace RM\PlantillaBundle\Parsers;


use RM\ClienteBundle\Entity\Cliente;
use RM\ComunicacionBundle\Entity\Comunicacion;
use RM\ComunicacionBundle\Entity\Creatividad;
use RM\ComunicacionBundle\Entity\InstanciaComunicacion;
use RM\PlantillaBundle\DependencyInjection\GeneraPlantillaComunicacion;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\PlantillaBundle\Entity\Slot;
use RM\PlantillaBundle\Model\Interfaces\GrupoSlotsInterface;
use RM\PlantillaBundle\Model\Interfaces\Parser\ParserInterface;
use RM\PlantillaBundle\Model\Interfaces\PlantillaInterface;
use RM\ProductoBundle\Entity\Producto;
use RM\ProductoBundle\Entity\Promocion;
use RM\RMMongoBundle\DependencyInjection\ManagerInstanciaComunicacionCliente;
use Symfony\Bridge\Twig\Extension\AssetExtension;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Templating\Asset\PackageInterface;


class EmailParser implements ParserInterface
{

    /**
     * @var PlantillaInterface
     */
    private $plantilla;

    /**
     * @var Comunicacion
     */
    private $comunicacion;

    /**
     * @var InstanciaComunicacion
     */
    private $instancia;

    /**
     * @var Crawler
     */
    private $crawler;

    /**
     * @var GeneraPlantillaComunicacion
     */
    private $plantillaGenerator;

    /**
     * @var ManagerInstanciaComunicacionCliente
     */
    private $manager;


    /**
     * @var Cliente
     */
    private $cliente;

    /** @var  string */
    private $empresa;

    /**
     * @var PackageInterface
     */
    private $asset;


    public function __construct(
        GeneraPlantillaComunicacion $plantillaGenerator,
        ManagerInstanciaComunicacionCliente $manager,
        TokenStorageInterface $token,
        AssetExtension $asset
        )
    {
        $this->crawler  = new Crawler();
        $this->manager    = $manager;
        $this->plantillaGenerator = $plantillaGenerator;
        $this->empresa = $token->getToken()->getUser()->getCliente();
        $this->asset = $asset;

        $this->document = new \DOMDocument();

    }

    /**
     * @param InstanciaComunicacion $instancia
     * @param Cliente               $cliente
     *
     * @return $this|mixed
     */
    public function parse(InstanciaComunicacion $instancia, Cliente $cliente)
    {
        $comunicacion = $instancia
            ->getIdSegmentoComunicacion()
            ->getIdComunicacion();

        $this->setComunicacion($comunicacion);
        $this->setInstancia($instancia);

        $plantilla = $comunicacion->getPlantilla();
        $this->setPlantilla($plantilla);
        $this->setCliente($cliente);

        $this->document = new \DOMDocument();

        if(file_exists($this->getRutaPlantillaGenerada())){
            return $this;
        }

        if(!file_exists($this->getRutaPlantilla())) {
            $this->plantillaGenerator->creaArchivoPlantilla($this->plantilla);
        }

        $this->document->preserveWhiteSpace = false;
        $this->document->loadHTML(file_get_contents($this->getRutaPlantilla()));

        $this->fillPlantilla();

        $this->document->formatOutput = true;
        $this->document->saveHTMLFile($this->getRutaPlantillaGenerada());

        return $this;

    }

    /**
     * @throws \Exception
     */
    private function fillPlantilla()
    {
        $gruposSlots = $this->plantilla->getGruposSlots();

        /** @var GrupoSlots $grupo */
        foreach($gruposSlots as $grupo)
        {
            $slots = $grupo->getSlots();

            /** @var Slot $slot */
            foreach($slots as $slot)
            {
                $this->fillSlot($grupo, $slot);
            }
        }
    }

    /**
     * @param GrupoSlotsInterface $grupo
     * @param Slot $slot
     * @throws \Exception
     */
    private function fillSlot(GrupoSlotsInterface $grupo, Slot $slot)
    {
        $promocion = $this->getPromocion($this->cliente->getIdCliente(), $slot->getIdSlot());

        $tipo = $grupo->getTipo();
        switch($tipo)
        {
            case GrupoSlots::PROMOCION:
                $this->fillSlotPromocion($grupo, $slot, $promocion);
                break;
            case GrupoSlots::CREATIVIDADES:
                $this->fillSlotCreatividad($grupo, $slot, $promocion);
                break;
            default:
                throw new \Exception(sprintf(
                        'No existen GruposSlots de tipo "%s"',
                        $tipo
                    ));
        }
    }

    /**
     * @param GrupoSlotsInterface $grupo
     * @param Slot $slot
     * @param Promocion $promocion
     * @throws \Exception
     */
    private function fillSlotPromocion(GrupoSlotsInterface $grupo, Slot $slot, Promocion $promocion)
    {
        $id = $this->getId($grupo, $slot);

        if($grupo->getMImgProducto()) {
            $this->fillNodoImagenProducto($id, $promocion);
        }

        if($grupo->getMNombreProducto()) {
            $this->fillNodoNombreProducto($id, $promocion);
        }

        if($grupo->getMPrecio()) {
            $this->fillNodoPrecio($id, $promocion);
        }

        if($grupo->getMTexto()) {
            $this->fillNodoTexto($id, $promocion);
        }

        if($grupo->getMCondiciones()) {
            $this->fillNodoCondiciones($id, $promocion);
        }

        if($grupo->getMFidelizacion()) {
            $this->fillNodoFidelizacion($id, $promocion);
        }

        if($grupo->getMVoucher()) {
           $this->fillNodoVoucher($id, $promocion);
        }

        if($grupo->getMVolumen()) {
            $this->fillNodoVolumen($id, $promocion);
        }

    }

    /**
     * @param GrupoSlotsInterface $grupo
     * @param Slot $slot
     * @param Promocion $promocion
     * @throws \Exception
     */
    private function fillSlotCreatividad(GrupoSlotsInterface $grupo, Slot $slot, Promocion $promocion)
    {
        $id = $this->getId($grupo, $slot);

        if($grupo->getMImgProducto()) {
            $this->fillNodoImagenProducto($id, $promocion);
        }

        if($grupo->getMTexto()) {
            $this->fillNodoTexto($id, $promocion);
        }
    }

    /**
     * @param GrupoSlotsInterface $grupo
     * @param Slot $slot
     * @return string
     */
    private function getId(GrupoSlotsInterface $grupo, Slot $slot)
    {
        return sprintf("%s-%s", 'slot',$slot->getIdSlot());
    }

    /**
     * @param $id
     * @return \DOMElement
     * @throws \Exception
     */
    private function getElementById($id)
    {
        $element = $this->document->getElementById($id);
        if(!$element) {
            throw new \Exception(sprintf(
                    'No se ha encontrado el elemento con id = "%s"', $id
                ));
        }

        return $element;
    }


    /**
     * @param Cliente $cliente
     */
    private function setCliente(Cliente $cliente)
    {
        $this->cliente = $cliente;
    }

    /**
     * @param PlantillaInterface $plantilla
     * @return $this
     */
    public function setPlantilla(PlantillaInterface $plantilla)
    {
        $this->plantilla = $plantilla;
        return $this;
    }

    /**
     * @param Comunicacion $comunicacion
     *
     * @return $this
     */
    public function setComunicacion(Comunicacion $comunicacion)
    {
        $this->comunicacion = $comunicacion;
        return $this;
    }

    /**
     * @param InstanciaComunicacion $instancia
     */
    public function setInstancia(InstanciaComunicacion $instancia)
    {
        $this->instancia = $instancia;
    }

    /**
     * @return string
     * @throws FileNotFoundException
     */
    public function getEstilosPlantilla()
    {
        if(!file_exists($this->getRutaPlantilla())) {
            throw new FileNotFoundException("No se ha encontrado la plantilla maquetada");
        }

        $crawler = new Crawler(file_get_contents($this->getRutaPlantilla()));
        $estilos = $crawler->filter('head>style');

        return $estilos->html();
    }

    /**
     * @return string
     */
    private function getRutaPlantilla()
    {
        return $this->plantillaGenerator
            ->getRutaPlantilla($this->plantilla);
    }

    /**
     * @return string
     */
    public function getRutaPlantillaGenerada()
    {
        return $this->getRutaComunicacionGenerada(). '/'. $this->cliente->getIdCliente().'.html';
    }

    /**
     * @return string
     */
    private function getRutaComunicacionGenerada()
    {
        $ruta =  $this
                ->plantillaGenerator
                ->getRutaCarpetaComunicacionesGeneradas().'/' .
                $this->instancia->getIdInstancia() ;

        if(!file_exists($ruta)) {
            mkdir($ruta, 0777, true);
        }

        return $ruta;
    }

    /**
     * @param $cliente
     * @param $slot
     * @return \RM\ProductoBundle\Entity\Promocion
     * @throws \Exception
     */
    private function getPromocion($cliente, $slot)
    {
        $promocion =  $this->manager
            ->findPromocionBySlotyCliente(
                $slot,
                $cliente,
                $this->instancia->getIdInstancia()
            );

        return $promocion;
    }

    /**
     * @param $id
     * @param Promocion $promocion
     */
    private function fillNodoImagenProducto($id, Promocion $promocion)
    {
        $imagen = $this->getElementById(sprintf("%s-imagen", $id));

        $producto = $promocion->getIdProducto();

        if(!$producto) {
            $creatividad = $promocion->getCreatividad();
            $imagen->setAttribute('src', $this->getRutaCreatividad($creatividad));
            return;
        }

        $grupo = $promocion->getNumPromocion()->getIdGrupo();

        $dimensiones = [
            $grupo->getIdTamanyoSlot()->getAncho(),
            $grupo->getIdTamanyoSlot()->getAlto()
        ];

        //Hace falta inyectar assetic
        $imagen->setAttribute('src', $this->getRutaImagen($producto, $dimensiones));

    }

    /**
     * @param Producto $producto
     *
     * @return string
     */
    private function getRutaImagen(Producto $producto, $dimensiones = [])
    {
        if(!$producto->getImagen()) {
            if(count($dimensiones) == 2) {
                return  "http://placehold.it/$dimensiones[0]x$dimensiones[1]";
            } else {
                return  "http://placehold.it/300x150";
            }
        }

        $path = sprintf('%s/%s/%s', $this->empresa, 'imagenesProducto', $producto->getImagen());
        return $this->absoluteRouteImagenProducto($path);
    }

    /**
     * @param Creatividad $creatividad
     *
     * @return string
     */
    private function getRutaCreatividad(Creatividad $creatividad)
    {
        $path = sprintf('%s/%s/%s', $this->empresa, 'imagenesCreatividad', $creatividad->getImagen());
        return $this->absoluteRouteImagenProducto($path);
    }

    /**
     * @param           $id
     * @param Promocion $promocion
     *
     * @throws \Exception
     */
    private function fillNodoNombreProducto($id, Promocion $promocion)
    {
        $nodo = $this->getElementById(sprintf("%s-nombreProducto", $id));

        $nombre = $promocion->getIdProducto()->getNombre();
        $nodo->nodeValue = $nombre;
    }

    /**
     * @param $id
     * @param Promocion $promocion
     */
    private function fillNodoPrecio($id, Promocion $promocion)
    {
        $nodo = $this->getElementById(sprintf("%s-precio", $id));

        $precio = $promocion->getIdProducto()->getPrecioVenta();
        $nodo->nodeValue = sprintf("%.2F € ", $precio);
    }

    /**
     * @param $id
     * @param Promocion $promocion
     */
    private function fillNodoVolumen($id, Promocion $promocion)
    {
        $nodo = $this->getElementById(sprintf("%s-volumen", $id));

        $volumen = $promocion;
        $nodo->nodeValue = '';
    }

    /**
     * @param $id
     * @param Promocion $promocion
     */
    private function fillNodoVoucher($id, Promocion $promocion)
    {
        $nodo = $this->getElementById(sprintf("%s-voucher", $id));

        $voucher = (string) $promocion->getVoucher();
        $nodo->nodeValue = $voucher;
    }



    /**
     * @param $id
     * @param Promocion $promocion
     */
    private function fillNodoCondiciones($id, Promocion $promocion)
    {
        $nodo = $this->getElementById(sprintf("%s-condiciones", $id));
        $condiciones = (string) $promocion->getCondiciones();

        $nodo->nodeValue = $condiciones;
    }

    /**
     * @param $id
     * @param Promocion $promocion
     */
    private function fillNodoFidelizacion($id, Promocion $promocion)
    {
        $nodo =  $this->getElementById(sprintf("%s-fidelizacion", $id));

        $fidelizacion = $promocion->getFidelizacion();
        $nodo->nodeValue = $fidelizacion;
    }

    /**
     * @param $id
     * @param Promocion $promocion
     */
    private function fillNodoTexto( $id, Promocion $promocion)
    {
        $nodo = $this->getElementById(sprintf("%s-texto", $id));
        $texto = (string) $promocion->getDescripcion();

        $nodo->nodeValue = $texto;

    }

    /**
     * @param $path
     *
     * @return string
     */
    private function absoluteRouteImagenProducto($path)
    {
        try {
            return $this->asset->getAssetUrl($path, null, $absolute = false);
        } catch (\Exception $e) {
            return '';
        }

    }


} 