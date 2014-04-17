<?php


namespace MFB\AccountBundle\Form\Factory;

use Symfony\Component\Form\FormFactoryInterface;

class FormFactory implements FactoryInterface
{
    private $formFactory;
    private $name;
    private $type;
    private $validationGroups;

    public function __construct(FormFactoryInterface $formFactory, $name, $type)
    {
        $this->formFactory = $formFactory;
        $this->name = $name;
        $this->type = $type;
    }

    public function createForm()
    {
        return $this->formFactory->createNamed(
            $this->name,
            $this->type,
            null,
            array('validation_groups' => $this->validationGroups)
        );
    }

    /**
     * @param mixed $validationGroups
     */
    public function setValidationGroups(array $validationGroups = null)
    {
        $this->validationGroups = $validationGroups;
    }


}