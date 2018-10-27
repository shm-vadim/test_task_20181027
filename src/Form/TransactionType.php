<?php

namespace App\Form;

use App\Entity\Transaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Service\TradeMaster;

class TransactionType extends AbstractType
{
    private $tradeMaster;

    public function __construct(TradeMaster $tradeMaster)
    {
        $this->tradeMaster = $tradeMaster;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('companyTicker', ChoiceType::class, [
                'choices' => $this->tradeMaster->getAllCompanyTickers()
            ])
            ->add('sharesCount')
            ->add('isBuy', ChoiceType::class, [
                'label' => 'Action',
                'choices' => [
                    'Buy' => true,
                    'Sell' => false
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
