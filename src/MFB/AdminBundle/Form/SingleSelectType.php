<?php
namespace MFB\AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SingleSelectType extends AbstractType
{

    private $choices;

    public function __construct($choices)
    {
        $this->choices = $choices;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
                ->add(
                    'choice',
                    'choice',
                    array(
                        'multiple' => false,
                        'expanded' => true,
                        'mapped' => false,
                        'choices' => $this->choices
                    )
                )
        ->add('submit', 'submit', array('label' => 'Submit'));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => null
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mfb_servicebundle_businessselect';
    }
}