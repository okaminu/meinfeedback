<?php

namespace MFB\FeedbackBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeedbackRatingType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rating', 'hidden')
            ->add('ratingCriteriaId', 'hidden')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'MFB\FeedbackBundle\Entity\FeedbackRating'
            ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mfb_feedbackbundle_feedbackrating';
    }
}
