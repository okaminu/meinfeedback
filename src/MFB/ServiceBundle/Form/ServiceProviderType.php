<?php

namespace MFB\ServiceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ServiceProviderType extends AbstractType
{

    private $honorific;

    public function __construct($honorific)
    {
        $this->honorific = $honorific;
    }

        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'honorific',
                'choice',
                array(
                    'choices' => $this->honorific,
                    'required' => true,
                    'multiple'  => false,
                    'empty_value' => false,
                    'expanded' => true,
                    'label' => 'Title'
                )
            )
            ->add('firstname', 'text', array('label' => 'Firstname', 'required' => false))
            ->add('lastname', 'text', array('label' => 'Lastname', 'required' => true))
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
