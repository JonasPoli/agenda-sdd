<?php

namespace App\Form;

use App\Entity\Categoria;
use App\Entity\Evento;
use App\Repository\CategoriaRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * O slug não faz parte do formulário: é gerado no prePersist e exibido só para leitura na edição.
 */
final class EventoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Evento|null $evento */
        $evento = $options['data'] ?? null;
        $categoriaAtual = $evento?->getCategoria();

        $builder
            ->add('titulo', TextType::class, [
                'label' => 'Título',
                'empty_data' => '',
            ])
            ->add('resumo', TextareaType::class, [
                'label' => 'Resumo',
                'required' => false,
                'help' => 'Até 255 caracteres. Aparece no card da página inicial.',
                'attr' => ['rows' => 2],
            ])
            ->add('descricao', TextareaType::class, [
                'label' => 'Descrição',
                'required' => false,
                'attr' => ['rows' => 8],
            ])
            // Fuso padrão da aplicação (America/Sao_Paulo) no modelo e na tela: o horário digitado é o gravado
            ->add('inicio', DateTimeType::class, [
                'label' => 'Data e hora de início',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'with_seconds' => false,
                'invalid_message' => 'Informe uma data e hora válidas.',
            ])
            ->add('local', TextType::class, [
                'label' => 'Local',
                'required' => false,
            ])
            // Só categorias ativas, mais a atual do evento em edição; outro id enviado é rejeitado pelo EntityType
            ->add('categoria', EntityType::class, [
                'label' => 'Categoria',
                'class' => Categoria::class,
                'choice_label' => 'nome',
                'placeholder' => 'Selecione…',
                'query_builder' => static fn (CategoriaRepository $repositorio) => $repositorio->queryAtivasOuAtual($categoriaAtual),
                'invalid_message' => 'Selecione uma categoria válida.',
            ])
            ->add('publicado', CheckboxType::class, [
                'label' => 'Publicado',
                'required' => false,
            ])
            // Sem limite no formulário: o limite de 3 vale só para o bloco da home
            ->add('destaque', CheckboxType::class, [
                'label' => 'Destaque',
                'required' => false,
                'help' => 'A página inicial mostra no topo até 3 destaques publicados, os mais próximos; os demais ficam na lista.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evento::class,
        ]);
    }
}
