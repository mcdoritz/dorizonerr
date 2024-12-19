<?php

namespace App\Form;

use App\Entity\MediaList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddMediaListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('url', TextType::class, [
                'label' => 'URL', // Optionnel : personnalisez le label
                'attr' => [
                    'placeholder' => 'Entrez une URL Youtube complète', // Optionnel : ajoute un placeholder
                ],
            ])
            ->add('x_last_videos', IntegerType::class, [
                'label' => 'Combien de vidéos à télécharger au maximum à chaque fois ?', // Optionnel : personnalisez le label
                'attr' => [
                    'placeholder' => 'nb. de vidéos', // Optionnel : ajoute un placeholder
                    'min' => 1, // Définit la valeur minimale à 1
                ],
            ])
            ->add('delete_after', IntegerType::class, [
                'label' => 'Supprimer les vidéos après : ', // Optionnel : personnalisez le label
                'attr' => [
                    'placeholder' => 'x', // Optionnel : ajoute un placeholder
                    'class' => 'short-input',
                    'min' => 0, // Définit la valeur minimale à 0
                ],
            ])
            ->add('cronjob', TextType::class, [
                'label' => 'Cronjob', // Optionnel : personnalisez le label
                'attr' => [
                    'placeholder' => 'Entrez un cronjob', // Optionnel : ajoute un placeholder
                ],
            ])
            ->add('quality', ChoiceType::class, [
                'label' => 'Qualité',
                'choices' => [
                    'Audio seulement' => 0,    // Texte affiché => Valeur envoyée
                    'Basse qualité vidéo (720p)' => 1,
                    'Bonne qualité vidéo (1080p)' => 2,
                    'Max qualité vidéo (4K et +) ' => 3,
                ],
                'placeholder' => 'Sélectionnez une qualité', // Optionnel : ajoute une option vide par défaut
                'expanded' => false, // Affiche un select (true pour des boutons radio)
                'multiple' => false, // Une seule sélection possible
            ])
            ->add('path', TextType::class, [
                'label' => 'Sélectionner un dossier',
                'attr' => [
                    'class' => 'folder-picker',
                    'placeholder' => 'Cliquez pour sélectionner un dossier',
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MediaList::class,
        ]);
    }
}
