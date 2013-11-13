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
            new MeinFeedback\HomeBundle\MeinFeedbackHomeBundle(),
            new MFB\AccountBundle\MFBAccountBundle(),
            new MFB\AdminBundle\MFBAdminBundle(),
            new MFB\ChannelBundle\MFBChannelBundle(),
            new MFB\CustomerBundle\MFBCustomerBundle(),
            new MFB\EmailBundle\MFBEmailBundle(),
            new MFB\FeedbackBundle\MFBFeedbackBundle(),
            new MFB\AccountProfileBundle\MFBAccountProfileBundle(),
            new MFB\WidgetBundle\MFBWidgetBundle(),
            new MFB\ReskribeBundle\MFBReskribeBundle(),
            new MFB\ServiceBundle\MFBServiceBundle(),
            new \Sensio\Bundle\BuzzBundle\SensioBuzzBundle()
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
