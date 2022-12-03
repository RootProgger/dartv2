<?php

namespace App\Form;

use App\Dto\EntryDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntryStep2Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if(false === in_array('leagueConfig', $options))
        {
            throw new \InvalidArgumentException('options must have at least "leagueConfig"');
        }

        $singleGames = $options['leagueConfig']?->getSingleGames();
        if(null === $singleGames)
        {
            throw new \InvalidArgumentException('singlegames must set in options');
        }

        $builder
            ->add('singlesHome', NumberType::class, [
                'label' => 'Heim',
                'html5' => true,
                'attr'=> [
                    'min' => 0,
                    'max' => $singleGames,
                    'step' => 1
                ]

            ])
            ->add('singlesGuest', NumberType::class, [
                'label' => 'Gast',
                'html5' => true,
                'attr'=> [
                    'min' => 0,
                    'max' => $singleGames,
                    'step' => 1
                ]
            ])

        ;


        $builder;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EntryDto::class,
            'leagueConfig' => null,
        ]);
    }
}
