<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),

            new VR\AppBundle\VRAppBundle(),
            new VR\DataMapperBundle\VRDataMapperBundle(),

            new VR\SugarCRMBundle\VRSugarCRMBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();

            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        $plugins = $this->registerPlugins();

        return array_merge($bundles, $plugins);
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    protected function registerPlugins()
    {
        $plugins = array();

        $classNames = $this->getPluginsBundlesClassNames();

        if (count($classNames)) {
            foreach ($classNames as $className) {
                $plugins[] = new $className();
            }
        }

        return $plugins;
    }

    protected function getPluginsBundlesClassNames()
    {
        $list = array();

        $path = __DIR__ . '/../plugins';

        if (!file_exists($path)) {
            return [];
        }

        $finder = new \Symfony\Component\Finder\Finder();
        $files = $finder->files()->in($path)->name('*Bundle.php');

        foreach ($files as $file) {
            $relativePathName = $file->getRelativePathName();
            $classPath = str_replace('.php', '', $relativePathName);
            $classPath = str_replace('/', '\\', $classPath);

            $list[] = $classPath;
        }

        return $list;
    }
}
