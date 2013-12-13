<?php
namespace MFB\ServiceBundle\Form;

use MFB\CustomerBundle\Form\CustomerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ServiceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', 'date', array(
                    'required' => false,
                    'input' => 'datetime',
                    'label' => 'Service Date',
                    'widget' => 'choice',
                    'data'  => new \DateTime('now')))
            ->add('serviceIdReference', 'text', array('required' => false))
            ->add('customer', new CustomerType())
            ->add('serviceGroup', 'entity', array(
                    'class' => 'MFBServiceBundle:ServiceGroup',
                    'property' => 'name'
                ))
            ->add('serviceProvider', 'entity', array(
                    'class' => 'MFBServiceBundle:ServiceProvider',
                    'property' => 'firstname'
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'MFB\ServiceBundle\Entity\Service'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mfb_servicebundle_service';
    }
}
