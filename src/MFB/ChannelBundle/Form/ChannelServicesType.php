<?php

namespace MFB\ChannelBundle\Form;

use MFB\ServiceBundle\Form\ServiceGroupVisibilityType;
use MFB\ServiceBundle\Form\ServiceProviderVisibilityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChannelServicesType extends AbstractType
{

        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('serviceProvider', 'collection', array('type' => new ServiceProviderVisibilityType()))
            ->add('serviceGroup', 'collection', array('type' => new ServiceGroupVisibilityType()))
            ->add('submit', 'submit', array('label' => 'Update'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MFB\ChannelBundle\Entity\AccountChannel'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mfb_channelbundle_servicesvisibility';
    }
}
