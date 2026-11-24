<?php

namespace App\Form;

use App\Entity\Categoria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CategoriaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nome', TextType::class, [
                'label' => 'Nome',
                'empty_data' => '',
            ])
            // Campo de texto (e não input color) para aceitar e validar o código digitado
            ->add('cor', TextType::class, [
                'label' => 'Cor',
                'empty_data' => '',
                'help' => 'Código hexadecimal no formato #RRGGBB, por exemplo #7C3AED.',
                'attr' => ['placeholder' => '#7C3AED'],
            ])
            ->add('ativa', CheckboxType::class, [
                'label' => 'Ativa',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categoria::class,
        ]);
    }
}
