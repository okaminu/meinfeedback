<?php

namespace MFB\ServiceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ServiceProviderType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', 'text', array('label' => 'Firstname'))
            ->add('lastname', 'text', array('label' => 'Lastname'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MFB\ServiceBundle\Entity\ServiceProvider'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mfb_servicebundle_serviceprovider';
    }
}
