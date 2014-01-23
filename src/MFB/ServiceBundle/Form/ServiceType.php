<?php
namespace MFB\ServiceBundle\Form;

use MFB\CustomerBundle\Form\CustomerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ServiceType extends AbstractType
{

    private $serviceProvider;

    private $serviceGroup;


    public function __construct($serviceProvider, $serviceGroup)
    {
        $this->serviceGroup = $serviceGroup;

        $this->serviceProvider = $serviceProvider;
    }
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
                    'property' => 'name',
                    'choices' => $this->serviceGroup
                ))
            ->add('serviceProvider', 'entity', array(
                    'class' => 'MFBServiceBundle:ServiceProvider',
                    'property' => 'lastname',
                    'label' => "Service Provider",
                    'choices' => $this->serviceProvider

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
