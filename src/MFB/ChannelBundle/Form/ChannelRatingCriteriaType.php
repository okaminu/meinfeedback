<?php

namespace MFB\ChannelBundle\Form;

use MFB\RatingBundle\Form\RatingType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChannelRatingCriteriaType extends AbstractType
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
                new RatingType()
            )
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
        return 'mfb_channelbundle_channelratingcriteria';
    }
}
