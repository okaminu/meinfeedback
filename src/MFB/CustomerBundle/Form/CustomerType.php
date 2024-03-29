<?php

namespace MFB\CustomerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use MFB\ServiceBundle\Form\ServiceType;

class CustomerType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array('required' => true, 'label' => 'Email'))
            ->add('anonymous', 'checkbox', array('required' => false))
            ->add('customerIdReference', 'text', array('required' => false))
            ->add(
                'gender',
                'choice',
                array(
                    'choices' => array('male' => 'Male', 'female' => 'Female'),
                    'required' => true,
                    'multiple'  => false,
                    'empty_value' => false,
                    'expanded' => true,
                    'label' => 'Gender'
                )
            )
            ->add('firstName', 'text', array('required' => false))
            ->add('lastName', 'text', array('required' => false))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'MFB\CustomerBundle\Entity\Customer'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mfb_customerbundle_customer';
    }
}
