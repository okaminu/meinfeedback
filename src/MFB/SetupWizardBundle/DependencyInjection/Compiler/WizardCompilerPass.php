<?php
namespace MFB\SetupWizardBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class WizardCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('mfb_setup_wizard.configurator.service')) {
            return;
        }

        $definition = $container->getDefinition('mfb_setup_wizard.configurator.service');

        foreach ($container->findTaggedServiceIds('mfb_setup_wizard.steps') as $id => $attributes) {
            $definition->addMethodCall('addStep', array(new Reference($id)));
        }
    }
}