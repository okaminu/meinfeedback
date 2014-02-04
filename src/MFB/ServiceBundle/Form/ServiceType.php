<?php
namespace MFB\ServiceBundle\Form;

use MFB\CustomerBundle\Form\CustomerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class ServiceType extends AbstractType
{

    private $serviceProvider;

    private $serviceType;


    public function __construct($serviceProvider, $serviceType)
    {
        $this->serviceType = $serviceType;

        $this->serviceProvider = $serviceProvider;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $begin = new \DateTime("-1 year");
        $end = new \DateTime('+1 month');

        $monthInterval = \DateInterval::createFromDateString('1 month');
        $period = new \DatePeriod($begin, $monthInterval, $end, \DatePeriod::EXCLUDE_START_DATE);

        $dateChoices = array();
        foreach ($period as $date) {
            $dateChoices["{$date->format('Y')}_{$date->format('m')}"] = "{$date->format('Y')}-{$date->format('F')}";
        }

        $builder
            ->add('date_YearMonth', 'choice', array(
                    'choices' => $dateChoices,
                    'required' => true,
                    'label' => 'Service Date',
                    'mapped' => false))
            ->add('date_Day', 'choice', array(
                    'choices' => array_combine(range(1, 30), range(1, 30)),
                    'required' => false,
                    'mapped' => false))
            ->add('date', 'date', array(
                    'input' => 'datetime',
                    'widget' => 'text',
                    'data'  => new \DateTime('now'),
                    'format' => 'y-M-d'))
            ->add('serviceIdReference', 'text', array('required' => false))
            ->add('customer', new CustomerType())
            ->add('serviceType', 'entity', array(
                    'class' => 'MFBServiceBundle:ServiceType',
                    'property' => 'name',
                    'choices' => $this->serviceType,
                ))
            ->add('serviceProvider', 'entity', array(
                    'class' => 'MFBServiceBundle:ServiceProvider',
                    'property' => 'lastname',
                    'label' => "Service Provider",
                    'choices' => $this->serviceProvider,
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'MFB\ServiceBundle\Entity\Service'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mfb_servicebundle_service';
    }
}
