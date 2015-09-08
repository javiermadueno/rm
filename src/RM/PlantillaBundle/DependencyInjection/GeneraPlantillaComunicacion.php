<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 19/02/2015
 * Time: 10:01
 */

namespace RM\PlantillaBundle\DependencyInjection;

use RM\PlantillaBundle\Entity\GrupoSlots;
use RM\PlantillaBundle\Entity\Plantilla;
use RM\PlantillaBundle\Entity\Slot;
use RM\PlantillaBundle\Model\Interfaces\PlantillaInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig_Environment as Twig;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;


class GeneraPlantillaComunicacion
{

    /**
     * @var Twig
     */
    private $twig;

    /**
     * @var string
     */
    private $webPath;

    /**
     * @var string
     */
    private $cliente;

    /**
     * @param Twig                  $twig
     * @param string                $webPath
     * @param TokenStorageInterface $security
     */
    public function __construct(Twig $twig, $webPath = '', TokenStorageInterface $security)
    {
        $this->twig = $twig;
        $this->webPath = $webPath;
        $this->cliente = $security->getToken()->getUser()->getCliente();
        $this->crawler = new Crawler();
    }

    /**
     * @param PlantillaInterface $plantilla
     *
     * @return $this
     */
    public function creaArchivoPlantilla(PlantillaInterface $plantilla)
    {
        $html = $this->renderPlantilla($plantilla);

        $nombreArchivoPlantilla =
            $this->getRutaCarpetaPlantillasCliente($this->cliente).'/'.$plantilla->getIdPlantilla().'.html';

        if(!file_put_contents($nombreArchivoPlantilla, $html)) {
            throw new FileNotFoundException(
                sprintf('No se ha podido escribir en el fichero %s', $nombreArchivoPlantilla));
        }

        return $this;
    }

    /**
     * @param PlantillaInterface $plantilla
     * @return string
     */
    public function renderPlantilla(PlantillaInterface $plantilla)
    {
        $html = $this->twig->render('RMPlantillaBundle:PlantillaComunicacion:plantillaEmail/plantillaEmail.html.twig', [
                'plantilla' => $plantilla
            ]);

        return $html;
    }

    /**
     * @return string
     */
    public  function getRutaCarpetaPlantillasCliente()
    {
        $ruta  = $this->webPath.'/' . $this->cliente . '/plantillas';

        if(!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }

        return $ruta;
    }

    public function getRutaCarpetaComunicacionesGeneradas()
    {
        $ruta  = $this->webPath.'/' . $this->cliente . '/comunicaciones_generadas';

        if(!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }

        return $ruta;
    }

    /**
     * @param PlantillaInterface $plantilla
     * @return string
     * @throws \Exception
     */
    public function getRutaPlantilla(PlantillaInterface $plantilla)
    {
        return $this->getRutaCarpetaPlantillasCliente().'/'.$plantilla->getIdPlantilla().'.html';
    }

    /**
     * @param PlantillaInterface $plantilla
     * @return array|string
     * @throws FileNotFoundException
     */
    public function compruebaPlantilla(PlantillaInterface $plantilla)
    {
        $rutaPlantilla = $this->getRutaPlantilla($plantilla);

        if(!file_exists($rutaPlantilla)){
           $this->creaArchivoPlantilla($plantilla);
        }

        $error = [];
        $crawler = new Crawler(file_get_contents($rutaPlantilla));

        $estilos = $crawler->filter('head>style');
        $estilos = $estilos->html();

        /** @var GrupoSlots $grupo */
        foreach($plantilla->getGruposSlots() as $grupo) {
            /** @var Slot $slot */
            foreach($grupo->getSlots() as $slot) {
                $div_slot = $crawler->filter(sprintf('#%s-%s',$grupo->getIdGrupo(),$slot->getIdSlot()));
                if(!count($div_slot)) {
                    $error[] = sprintf('Falta el div con ID = "%s-%s" ',
                        $grupo->getIdGrupo(),$slot->getIdSlot());
                    continue;
                }

                if($grupo->getMImgMarca()) {
                    if(!count($div_slot->filter('.imagenMarca'))) {
                        $error[] = sprintf('Falta el div de imagenMarca para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(),$slot->getIdSlot());
                    }
                }

                if($grupo->getMImgProducto()) {
                    if(!count($div_slot->filter('.imagenProducto'))) {
                        $error[] = sprintf('Falta el div de imagenProducto para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(),$slot->getIdSlot());
                    }
                }

                if($grupo->getMPrecio()) {
                    if(!count($div_slot->filter('.precio'))) {
                        $error[] = sprintf('Falta el div de precio para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(),$slot->getIdSlot());
                    }
                }

                if($grupo->getMTexto()) {
                    if(!count($div_slot->filter('.texto'))) {
                        $error[] = sprintf('Falta el div de texto para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(),$slot->getIdSlot());
                    }
                }

                if($grupo->getMCondiciones()) {
                    if(!count($div_slot->filter('.condiciones'))) {
                        $error[] = sprintf('Falta el div de condiciones para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(),$slot->getIdSlot());
                    }
                }

                if($grupo->getMFidelizacion()) {
                    if(!count($div_slot->filter('.fidelizacion'))) {
                        $error[] = sprintf('Falta el div de fidelizacion para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(),$slot->getIdSlot());
                    }
                }

                if($grupo->getMVoucher()) {
                    if(!count($div_slot->filter('.voucher'))) {
                        $error[] = sprintf('Falta el div de Voucher para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(),$slot->getIdSlot());
                    }
                }

                if($grupo->getMVolumen()) {
                    if(!count($div_slot->filter('.volumen'))) {
                        $error[] = sprintf('Falta el div de volumen para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(),$slot->getIdSlot());
                    }
                }

            }
        }

        if(!empty($error)) {
            return $error;
        }

        return [];
    }

    public function setPlantilla(PlantillaInterface $plantilla)
    {
        $this->plantilla =  $plantilla;
        return $this;
    }

    private function guardaPlantilla(Crawler $crawler)
    {
    }


} 