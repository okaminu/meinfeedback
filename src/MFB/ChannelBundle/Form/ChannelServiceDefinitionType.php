<?php

namespace MFB\ChannelBundle\Form;

use MFB\RatingBundle\Form\RatingType;
use MFB\ServiceBundle\Form\ServiceDefinitionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChannelServiceDefinitionType extends AbstractType
{

     /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'ServiceDefinition',
                new ServiceDefinitionType()
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
