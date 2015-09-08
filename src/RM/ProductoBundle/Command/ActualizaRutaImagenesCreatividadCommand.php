<?php
/**
 * Created by PhpStorm.
 * User: jmadueno
 * Date: 07/07/2015
 * Time: 11:18
 */

namespace RM\ProductoBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Helper\ProgressBar;

class ActualizaRutaImagenesCreatividadCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('creatividad:actualiza:imagenes')
            ->setDescription('Actualiza el nombre de las imagenes en la base de datos')
            ->addArgument(
                'id_cliente',
                InputArgument::REQUIRED,
                'Id del cliente cuyos productos se quieren actualizar'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $id_cliente = $input->getArgument('id_cliente');
        $output->writeln(sprintf('<comment>Actualizando imagenes del cliente "%s"</comment>', $id_cliente));

        $container = $this->getContainer();

        $em = $container->get('doctrine')->getManager($id_cliente);

        $web_path = $container->getParameter('web_path');
        $ruta_imagenes = $web_path . '/' . $id_cliente . '/' . 'imagenesCreatividad/';

        $imagenes = scandir($ruta_imagenes);
        $imagenes = new ArrayCollection($imagenes);
        $imagenes = $imagenes->filter(function($imagen){
            return (bool) preg_match('/([^\s]+(\.(?i)(jpe?g|png|gif|bmp|tiff|tif))$)/', $imagen);
        });

        $numero_ficheros = $imagenes->count();

        $progressBar = new ProgressBar($output, $numero_ficheros);

        $qb = $em->getRepository('RMComunicacionBundle:Creatividad')
            ->createQueryBuilder('creatividad')
            ->update()
            ->set('creatividad.imagen', ':imagen');

        $progressBar->start();


        foreach ($imagenes as $image) {
            $name = $image;
            $progressBar->advance();

            $info = preg_split('/\./', $name);
            $id_creatividad = isset($info[0]) ? $info[0] : null;

            if (!is_numeric($id_creatividad)) {
                continue;
            }

            $qb->setParameter('imagen', $name)
                ->where('creatividad.idCreatividad = :creatividad')
                ->setParameter('creatividad', $id_creatividad)
                ->getQuery()->execute();
            ;
        }

        $progressBar->finish();
        $output->writeln('<fg=white;bg=green>Se han importado las imagenes correctamente</fg=white;bg=green>');

    }

} 