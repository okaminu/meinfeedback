<?php

namespace MFB\ChannelBundle\Form;

use MFB\RatingBundle\Form\RatingType;
use MFB\ServiceBundle\Form\ServiceDefinitionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChannelServiceDefinitionType extends AbstractType
{

    private $definitionChoices;

    public function __construct($choices)
    {
        $this->definitionChoices = $choices;
    }
     /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'serviceDefinition',
                'entity',
                array(
                    'class' => 'MFBServiceBundle:ServiceDefinition',
                    'property' => 'name',
                    'choices' => $this->definitionChoices,
                    'label' => 'Select services',
                    'empty_value' => '--- I will insert my own service name ---',
                    'empty_data' => null,
                    'data' => 'customServiceDefName',
                    'required' => false
                )
            )
            ->add(
                'customDefName',
                'text',
                array(
                    'mapped' => false,
                    'required' => false,
                    'label' => 'Or insert a custom service name'
                )
            )
            ->add(
                'submit',
                'submit',
                array('label' => "Add")
            )
        ;
    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MFB\ChannelBundle\Entity\ChannelServiceDefinition'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mfb_channelbundle_channelservicedefinition';
    }
}
