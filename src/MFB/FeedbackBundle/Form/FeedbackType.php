<?php

namespace MFB\FeedbackBundle\Form;

use MFB\CustomerBundle\Form\CustomerType;
use MFB\ServiceBundle\Form\ServiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FeedbackType extends AbstractType
{
    private $serviceType;
    
    public function __construct(ServiceType $serviceType)
    {
        $this->serviceType = $serviceType;
    }
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', 'textarea')
            ->add('service', $this->serviceType)
            ->add('save', 'submit', array('label' => 'Send'))
            ->add('feedbackRating', 'collection', array('type' => new FeedbackRatingType()));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MFB\FeedbackBundle\Entity\Feedback'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mfb_feedbackbundle_feedback';
    }
}
