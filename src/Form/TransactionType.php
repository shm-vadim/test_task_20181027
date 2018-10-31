<?php

namespace App\Form;

use App\Entity\Transaction;
use App\Service\TradeMaster;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                'choices' => $this->tradeMaster->getAllCompaniesTickers(),
            ])
            ->add('sharesCount')
            ->add('isBuy', ChoiceType::class, [
                'label' => 'Action',
                'mapped' => false,
                'choices' => [
                    'Buy' => true,
                    'Sell' => false,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
