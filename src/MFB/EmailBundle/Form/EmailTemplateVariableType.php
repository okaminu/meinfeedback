<?php

namespace MFB\EmailBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EmailTemplateVariableType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isActive', 'choice', array('choices' => array('0' => 'Disable', '1' => 'Enable')))
            ->add('type', 'hidden')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MFB\EmailBundle\Entity\EmailTemplateVariable'
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
