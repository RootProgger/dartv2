<?php

namespace App\Form;

use App\Dto\EntryDto;
use App\Dto\EntryStep1Dto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class EntryStep1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if(false === in_array('leagueConfig', $options))
        {
            throw new \InvalidArgumentException('options must have atr least "leagueConfig"');
        }

        $games = $options['leagueConfig']?->getDoubleGames();
        if(null === $games)
        {
            throw new \InvalidArgumentException('Doublegames must set in LeagueConfig');
        }

        $builder
            ->add('doublesHome', NumberType::class, [
                'label' => 'Heim',
                'html5' => true,
                'attr'=> [
                    'min' => 0,
                    'max' => $games,
                    'step' => 1
                ]

            ])
            ->add('doublesGuest', NumberType::class, [
                'label' => 'Gast',
                'html5' => true,
                'attr'=> [
                    'min' => 0,
                    'max' => $games,
                    'step' => 1
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EntryDto::class,
            'leagueConfig' => null,
            'validation_groups' => [
                'step1'
            ]
        ]);
    }
}
