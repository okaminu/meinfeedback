<?php


namespace MFB\AccountBundle\Form\Factory;

interface FactoryInterface
{
    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createForm();
}
