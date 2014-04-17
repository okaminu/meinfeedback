<?php

namespace MFB\EmailBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VariableType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('variables', 'collection', array('type' => new EmailTemplateVariableType()))
            ->add('submit', 'submit', array('label' => 'Next'))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MFB\EmailBundle\Entity\EmailTemplate'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mfb_emailbundle_emailtemplatevariable';
    }
}
