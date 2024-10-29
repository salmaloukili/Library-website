<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchBookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                    'Title' => 'bookTitle',
                    'Author' => 'authorName',
                    'Pages' => 'numberOfPages',
                    'Language' => 'language',
                    'Publisher' => 'publisher',
                    'Publication Date' => 'publicationDate',
                    'Genre' => 'genre'
                ]])
            ->add('value', TextType::class, [
                'label' => 'type keyword',
                'mapped' => false,
            ])
            ->add('search', SubmitType::class, ['label' => 'Search']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}