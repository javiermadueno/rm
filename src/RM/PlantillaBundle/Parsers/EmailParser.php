<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 05/03/2015
 * Time: 16:45
 */

namespace RM\PlantillaBundle\Parsers;


use RM\ClienteBundle\Entity\Cliente;
use RM\ComunicacionBundle\Entity\Creatividad;
use RM\PlantillaBundle\DependencyInjection\GeneraPlantillaComunicacion;
use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\PlantillaBundle\Entity\Slot;
use RM\PlantillaBundle\Model\Interfaces\GrupoSlotsInterface;
use RM\PlantillaBundle\Model\Interfaces\Parser\ParserInterface;
use RM\PlantillaBundle\Model\Interfaces\PlantillaInterface;
use RM\ProductoBundle\Entity\Producto;
use RM\ProductoBundle\Entity\Promocion;
use RM\RMMongoBundle\DependencyInjection\ManagerInstanciaComunicacionCliente;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Finder\Finder;


class EmailParser implements ParserInterface
{

    /**
     * @var PlantillaInterface
     */
    private $plantilla;

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


    public function __construct(
        GeneraPlantillaComunicacion $plantillaGenerator,
        ManagerInstanciaComunicacionCliente $manager
    ) {
        $this->crawler = new Crawler();
        $this->manager = $manager;
        $this->plantillaGenerator = $plantillaGenerator;

        $this->document = new \DOMDocument();

    }

    /**
     * @param PlantillaInterface $plantilla
     * @param Cliente            $cliente
     *
     * @return mixed|void
     */
    public function parse(PlantillaInterface $plantilla, Cliente $cliente)
    {

        $this->document = new \DOMDocument();

        $this->setPlantilla($plantilla);
        $this->setCliente($cliente);

        if (file_exists($this->getRutaPlantillaGenerada())) {
            return $this;
        }

        if (!file_exists($this->getRutaPlantilla())) {
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
        foreach ($gruposSlots as $grupo) {
            $slots = $grupo->getSlots();

            /** @var Slot $slot */
            foreach ($slots as $slot) {
                $this->fillSlot($grupo, $slot);
            }
        }
    }

    /**
     * @param GrupoSlotsInterface $grupo
     * @param Slot                $slot
     *
     * @throws \Exception
     */
    private function fillSlot(GrupoSlotsInterface $grupo, Slot $slot)
    {
        $promocion = $this->getPromocion($this->cliente->getIdCliente(), $slot->getIdSlot());

        $tipo = $grupo->getTipo();
        switch ($tipo) {
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
     * @param Slot                $slot
     * @param Promocion           $promocion
     *
     * @throws \Exception
     */
    private function fillSlotPromocion(GrupoSlotsInterface $grupo, Slot $slot, Promocion $promocion)
    {
        $id = $this->getId($grupo, $slot);

        $element = $this->getElementById($id);

        if ($grupo->getMImgMarca()) {
            $this->fillNodoImagenMarca($id, $promocion);
        }

        if ($grupo->getMImgProducto()) {
            $this->fillNodoImagenProducto($id, $promocion);
        }

        if ($grupo->getMPrecio()) {
            $this->fillNodoPrecio($id, $promocion);
        }

        if ($grupo->getMTexto()) {
            $this->fillNodoTexto($id, $promocion);
        }

        if ($grupo->getMCondiciones()) {
            $this->fillNodoCondiciones($id, $promocion);
        }

        if ($grupo->getMFidelizacion()) {
            $this->fillNodoFidelizacion($id, $promocion);
        }

        if ($grupo->getMVoucher()) {
            $this->fillNodoVoucher($id, $promocion);
        }

        if ($grupo->getMVolumen()) {
            $this->fillNodoVolumen($id, $promocion);
        }

    }

    /**
     * @param GrupoSlotsInterface $grupo
     * @param Slot                $slot
     * @param Promocion           $promocion
     *
     * @throws \Exception
     */
    private function fillSlotCreatividad(GrupoSlotsInterface $grupo, Slot $slot, Promocion $promocion)
    {
        $id = $this->getId($grupo, $slot);

        $element = $this->getElementById($id);

        if ($grupo->getMImgProducto()) {
            $this->fillNodoImagenProducto($id, $promocion);
        }

        if ($grupo->getMTexto()) {
            $this->fillNodoTexto($id, $promocion);
        }
    }

    /**
     * @param GrupoSlotsInterface $grupo
     * @param Slot                $slot
     *
     * @return string
     */
    private function getId(GrupoSlotsInterface $grupo, Slot $slot)
    {
        return sprintf("%s-%s", $grupo->getIdGrupo(), $slot->getIdSlot());
    }

    /**
     * @param $id
     *
     * @return \DOMElement
     * @throws \Exception
     */
    private function getElementById($id)
    {
        $element = $this->document->getElementById($id);
        if (!$element) {
            throw new \Exception(sprintf(
                'No se ha encontrado el elemento con id = "%s"', $id
            ));
        }

        return $element;
    }

    /**
     * @param \DOMDocument $document
     */
    private function setDOMDocument(\DOMDocument $document)
    {
        $this->document = $document;
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
     *
     * @return $this
     */
    public function setPlantilla(PlantillaInterface $plantilla)
    {
        $this->plantilla = $plantilla;
        return $this;
    }

    /**
     * @return string
     * @throws FileNotFoundException
     */
    public function getEstilosPlantilla()
    {
        if (!file_exists($this->getRutaPlantilla())) {
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
        return $this->plantillaGenerator
            ->getRutaCarpetaComunicacionesGeneradas() . '/' . $this->cliente->getIdCliente() . '.html';

    }

    /**
     * @param $cliente
     * @param $slot
     *
     * @return \RM\ProductoBundle\Entity\Promocion
     * @throws \Exception
     */
    private function getPromocion($cliente, $slot)
    {
        $promocion = $this->manager
            ->findPromocionBySlotyCliente($slot, $cliente);

        return $promocion;
    }

    /**
     * @param           $id
     * @param Promocion $promocion
     */
    private function fillNodoImagenProducto($id, Promocion $promocion)
    {
        $imagen = $this->getElementById(sprintf("%s-imagenProducto", $id));

        $producto = $promocion->getIdProducto();

        if (!$producto) {
            $creatividad = $promocion->getCreatividad();
            $imagen->setAttribute('src', $this->getRutaCreatividad($creatividad));
            return;
        }

        //Hace falta inyectar assetic
        $imagen->setAttribute('src', $this->getRutaImagen($producto));

    }

    private function getRutaImagen(Producto $producto)
    {
        $finder = new Finder();

        $finder->in(__DIR__ . '/../../../../web/3/imagenesProducto')->files();

        $file = $finder->name(sprintf('%s.*', $producto->getIdProducto()));

        foreach ($file as $fil) {
            return '/RM2/web/3/imagenesProducto/' . $fil->getRelativePathName();
        }

        return '';
    }

    private function getRutaCreatividad(Creatividad $creatividad)
    {
        $finder = new Finder();

        $finder->in(__DIR__ . '/../../../../web/3/imagenesCreatividad')->files();

        $file = $finder->name(sprintf('%s.*', $creatividad->getIdCreatividad()));

        foreach ($file as $fil) {
            return '/RM2/web/3/imagenesCreatividad/' . $fil->getRelativePathName();
        }

        return '';
    }

    /**
     * @param           $id
     * @param Promocion $promocion
     */
    private function fillNodoPrecio($id, Promocion $promocion)
    {
        $nodo = $this->getElementById(sprintf("%s-precio", $id));

        $precio = $promocion->getIdProducto()->getPrecioVenta();
        $nodo->nodeValue = sprintf("%.2F ", $precio) . ' ' . htmlentities('&euro;', ENT_HTML5, 'UTF-8');
    }

    /**
     * @param           $id
     * @param Promocion $promocion
     */
    private function fillNodoVolumen($id, Promocion $promocion)
    {
        $nodo = $this->getElementById(sprintf("%s-volumen", $id));

        $volumen = $promocion;
        $nodo->nodeValue = '';
    }

    /**
     * @param           $id
     * @param Promocion $promocion
     */
    private function fillNodoVoucher($id, Promocion $promocion)
    {
        $nodo = $this->getElementById(sprintf("%s-voucher", $id));

        $voucher = (string)$promocion->getVoucher();
        $nodo->nodeValue = $voucher;
    }

    /**
     * @param           $id
     * @param Promocion $promocion
     */
    private function fillNodoImagenMarca($id, Promocion $promocion)
    {
        $nodo = $this->getElementById(sprintf("%s-imagenMarca", $id));

        $marca = $promocion->getIdProducto()->getIdMarca()->getIdMarca();
        $nodo->setAttribute('src', 'prueba');
    }

    /**
     * @param           $id
     * @param Promocion $promocion
     */
    private function fillNodoCondiciones($id, Promocion $promocion)
    {
        $nodo = $this->getElementById(sprintf("%s-condiciones", $id));
        $condiciones = (string)$promocion->getCondiciones();

        $nodo->nodeValue = $condiciones;
    }

    /**
     * @param           $id
     * @param Promocion $promocion
     */
    private function fillNodoFidelizacion($id, Promocion $promocion)
    {
        $nodo = $this->getElementById(sprintf("%s-fidelizacion", $id));

        $fidelizacion = $promocion->getFidelizacion();
        $nodo->nodeValue = $fidelizacion;
    }

    /**
     * @param           $id
     * @param Promocion $promocion
     */
    private function fillNodoTexto($id, Promocion $promocion)
    {
        $nodo = $this->getElementById(sprintf("%s-texto", $id));
        $texto = (string)$promocion->getDescripcion();

        $nodo->nodeValue = $texto;

    }


} 