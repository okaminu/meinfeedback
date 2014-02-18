<?php

namespace MFB\ChannelBundle;

use MFB\SetupWizardBundle\DependencyInjection\Compiler\WizardCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MFBChannelBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new WizardCompilerPass());
    }
}
