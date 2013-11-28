<?php
/**
 * Created by PhpStorm.
 * User: aurimas
 * Date: 28/11/2013
 * Time: 11:16
 */
namespace MFB\Template\Interfaces;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Translation\TranslatorInterface;

interface TemplateManagerInterface
{
    public function getTemplate($accountId, $templateTypeId, $name, ObjectManager $em, TranslatorInterface $translator);
}