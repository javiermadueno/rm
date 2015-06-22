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
use Symfony\Component\Security\Core\SecurityContextInterface;
use Twig_Environment as Twig;


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
     * @param Twig   $twig
     * @param string $webPath
     */
    public function __construct(Twig $twig, $webPath = '', SecurityContextInterface $security)
    {
        $this->twig = $twig;
        $this->webPath = $webPath;
        $this->cliente = $security->getToken()->getUser()->getCliente();
        $this->crawler = new Crawler();
    }

    public function getRutaCarpetaComunicacionesGeneradas()
    {
        $ruta = $this->webPath . '/' . $this->cliente . '/comunicaciones_generadas';

        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }

        return $ruta;
    }

    /**
     * @param PlantillaInterface $plantilla
     *
     * @return array|string
     * @throws \FileNotFoundException
     */
    public function compruebaPlantilla(PlantillaInterface $plantilla)
    {
        $rutaPlantilla = $this->getRutaPlantilla($plantilla);

        if (!file_exists($rutaPlantilla)) {
            $this->creaArchivoPlantilla($plantilla);
        }

        $error = [];
        $crawler = new Crawler(file_get_contents($rutaPlantilla));

        $estilos = $crawler->filter('head>style');
        $estilos = $estilos->html();

        /** @var GrupoSlots $grupo */
        foreach ($plantilla->getGruposSlots() as $grupo) {
            /** @var Slot $slot */
            foreach ($grupo->getSlots() as $slot) {
                $div_slot = $crawler->filter(sprintf('#%s-%s', $grupo->getIdGrupo(), $slot->getIdSlot()));
                if (!count($div_slot)) {
                    $error[] = sprintf('Falta el div con ID = "%s-%s" ',
                        $grupo->getIdGrupo(), $slot->getIdSlot());
                    continue;
                }

                if ($grupo->getMImgMarca()) {
                    if (!count($div_slot->filter('.imagenMarca'))) {
                        $error[] = sprintf('Falta el div de imagenMarca para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(), $slot->getIdSlot());
                    }
                }

                if ($grupo->getMImgProducto()) {
                    if (!count($div_slot->filter('.imagenProducto'))) {
                        $error[] = sprintf('Falta el div de imagenProducto para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(), $slot->getIdSlot());
                    }
                }

                if ($grupo->getMPrecio()) {
                    if (!count($div_slot->filter('.precio'))) {
                        $error[] = sprintf('Falta el div de precio para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(), $slot->getIdSlot());
                    }
                }

                if ($grupo->getMTexto()) {
                    if (!count($div_slot->filter('.texto'))) {
                        $error[] = sprintf('Falta el div de texto para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(), $slot->getIdSlot());
                    }
                }

                if ($grupo->getMCondiciones()) {
                    if (!count($div_slot->filter('.condiciones'))) {
                        $error[] = sprintf('Falta el div de condiciones para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(), $slot->getIdSlot());
                    }
                }

                if ($grupo->getMFidelizacion()) {
                    if (!count($div_slot->filter('.fidelizacion'))) {
                        $error[] = sprintf('Falta el div de fidelizacion para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(), $slot->getIdSlot());
                    }
                }

                if ($grupo->getMVoucher()) {
                    if (!count($div_slot->filter('.voucher'))) {
                        $error[] = sprintf('Falta el div de Voucher para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(), $slot->getIdSlot());
                    }
                }

                if ($grupo->getMVolumen()) {
                    if (!count($div_slot->filter('.volumen'))) {
                        $error[] = sprintf('Falta el div de volumen para el slot con ID = "%s-%s" ',
                            $grupo->getIdGrupo(), $slot->getIdSlot());
                    }
                }

            }
        }

        if (!empty($error)) {
            return $error;
        }

        return [];
    }

    /**
     * @param PlantillaInterface $plantilla
     *
     * @return string
     * @throws \Exception
     */
    public function getRutaPlantilla(PlantillaInterface $plantilla)
    {
        return $this->getRutaCarpetaPlantillasCliente($this->cliente) . '/' . $plantilla->getIdPlantilla() . '.html';
    }

    /**
     * @param string $cliente
     *
     * @return string
     * @throws \Exception
     */
    private function getRutaCarpetaPlantillasCliente($cliente = '')
    {
        $ruta = $this->webPath . '/' . $this->cliente . '/plantillas';

        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }

        return $ruta;
    }

    /**
     * @param Plantilla $plantilla
     * @param string    $cliente
     *
     * @throws \Exception
     * @throws \FileNotFoundException
     */
    public function creaArchivoPlantilla(PlantillaInterface $plantilla, $cliente = '')
    {
        $html = $this->renderPlantilla($plantilla);

        $nombreArchivoPlantilla =
            $this->getRutaCarpetaPlantillasCliente($this->cliente) . '/' . $plantilla->getIdPlantilla() . '.html';

        if (!file_put_contents($nombreArchivoPlantilla, $html)) {
            throw new \FileNotFoundException(
                sprintf('No se ha podido escribir en el fichero %s', $nombreArchivoPlantilla));
        }
    }

    /**
     * @param PlantillaInterface $plantilla
     *
     * @return string
     */
    public function renderPlantilla(PlantillaInterface $plantilla)
    {
        $html = $this->twig->render('RMPlantillaBundle:PlantillaComunicacion:plantillaEmail/plantillaEmail.html.twig', [
            'plantilla' => $plantilla
        ]);

        return $html;
    }

    public function setPlantilla(PlantillaInterface $plantilla)
    {
        $this->plantilla = $plantilla;
        return $this;
    }

    private function guardaPlantilla(Crawler $crawler)
    {
    }


} 