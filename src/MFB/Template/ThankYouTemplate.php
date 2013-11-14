<?php

namespace MFB\Template;

use MFB\Template\Placeholder\PlaceholderContainer;

class ThankYouTemplate {

    protected $template;

    protected $container;

    protected $customer;

    protected $content;

    public function __construct()
    {
        $this->container = PlaceholderContainer::getPreparedContainer();
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function fillValues()
    {
        $this->getPlaceholder('firstname')
            ->setUserDataValue(
                $this->getCustomer()->getFirstName()
            )
        ;

        $this->getPlaceholder('lastname')
            ->setUserDataValue(
                $this->getCustomer()->getLastname()
            )
        ;

        $this->getPlaceholder('sal')
            ->setUserDataValue(
                $this->getCustomer()->getSalutation()
            )
        ;

        //        $this->getPlaceholder('homepage_url')
        //            ->setUserDataValue(
        //                $this->getCustomer()->getHomepage()
        //            )
        //        ;

        return $this;
    }

    /**
     * Shortcut method for container getPlaceholder
     *
     * @param $name
     * @return mixed
     */
    public function getPlaceholder($name)
    {
        return $this->getContainer()->getPlaceholder($name);
    }

    /**
     * Get translation
     * @return mixed
     */
    public function getTranslation()
    {
        $this->fillValues();

        return $this->getContainer()->getTranslation($this->getContent());
    }

    /**
     * @param mixed $content
     * @return $this;
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $customer
     * @return $this;
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

}