<?php


namespace MFB\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ChangePasswordFormType extends AbstractType
{
    private $class;

    /**
     * @param string $class The User class name
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $constraint = new UserPassword();

        $builder->add(
            'current_password',
            'password',
            array(
                'label' => 'Old password',
                'translation_domain' => 'MFBAccountBundle',
                'mapped' => false,
                'constraints' => $constraint,
                'attr' => array(
                    'class' => 'required validate-passwd validate-alphanum fr_input_txt fr_97_5pro popupInput form-control',
                    'placeholder' => 'Old password'
                )
            )
        );
        $builder->add(
            'plainPassword',
            'repeated',
            array(
                'type' => 'password',
                'options' => array('translation_domain' => 'MFBAccountBundle'),
                'first_options' => array(
                    'label' => 'New password',
                    'attr' => array(
                        'class' => 'required validate-passwd validate-alphanum fr_input_txt fr_97_5pro popupInput form-control',
                        'placeholder' => 'New password'
                    )
                ),
                'second_options' => array(
                    'label' => 'Repeat new password',
                    'attr' => array(
                        'class' => 'required validate-passwd validate-alphanum fr_input_txt fr_97_5pro popupInput form-control',
                        'placeholder' => 'Repeat new password'
                    )
                ),
                'invalid_message' => 'Your passwords should match.',

            )
        );
        $builder->add(
            'save',
            'submit',
            array(
                'label' => 'Change password',
                'attr' => array(
                    "class" => "btn btn-left orange abstandoben breite210 width100"
                )
            )
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => $this->class,
                'intention'  => 'change_password',
            )
        );
    }

    public function getName()
    {
        return 'mfb_account_change_password';
    }
}