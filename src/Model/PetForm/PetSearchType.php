<?php

namespace App\Model\PetForm;

use App\Model\Dto\PetCombinedDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PetSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => false,
                'label' => 'Enter filter by Name:',
            ])
            ->add('description', TextType::class, [
                'required' => false,
                'label' => 'Enter filter by Description:',
            ])
            ->add('owner', TextType::class, [
                'required' => false,
                'label' => 'Enter filter by Owner:',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PetCombinedDto::class,
        ]);
    }
}
