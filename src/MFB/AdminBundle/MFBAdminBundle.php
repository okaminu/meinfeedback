<?php

namespace MFB\AdminBundle;

use MFB\SetupWizardBundle\DependencyInjection\Compiler\WizardCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MFBAdminBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new WizardCompilerPass());
    }
}
