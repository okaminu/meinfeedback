<?php

namespace MFB\ChannelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChannelRatingSelectType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'ratingCriteria',
                'entity',
                array(
                    'class' => 'MFBRatingBundle:Rating',
                    'property' => 'name'
                )
            )
            ->add('submit', 'submit')
        ;
    }


    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MFB\ChannelBundle\Entity\ChannelRatingCriteria'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mfb_channelbundle_channelratingselect';
    }
}