<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Ideup\SimplePaginatorBundle\IdeupSimplePaginatorBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new Ob\HighchartsBundle\ObHighchartsBundle(),
            new RM\LinealesBundle\RMLinealesBundle(),
            new RM\TransformadasBundle\RMTransformadasBundle(),
            new RM\DiscretasBundle\RMDiscretasBundle(),
            new RM\CategoriaBundle\RMCategoriaBundle(),
            new RM\ProductoBundle\RMProductoBundle(),
            new RM\ComunicacionBundle\RMComunicacionBundle(),
            new RM\SegmentoBundle\RMSegmentoBundle(),
            new RM\PlantillaBundle\RMPlantillaBundle(),
            new RM\ClienteBundle\RMClienteBundle(),
            new IMAG\LdapBundle\IMAGLdapBundle(),
            new RM\StaticBundle\RMStaticBundle(),
            new RM\InsightBundle\RMInsightBundle(),
            new RM\ProcesosBundle\ProcesosBundle(),
            new Doctrine\Bundle\MongoDBBundle\DoctrineMongoDBBundle(),
            new RM\RMMongoBundle\RMMongoBundle(),
            new RM\AppBundle\RMAppBundle(),
            new RM\LdapBundle\LdapBundle(),
            new RM\TrackingBundle\RMTrackingBundle(),
            new Bazinga\Bundle\GeocoderBundle\BazingaGeocoderBundle(),
            new RM\AdminBundle\RMAdminBundle(),
            new Lexik\Bundle\TranslationBundle\LexikTranslationBundle(),

        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'])) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();

         }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
    }
}
