<?php

namespace App\Form;

use App\Entity\Skill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkillFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Skill Name',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'e.g., JavaScript, Project Management, Public Speaking'
                ],
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Skill Category',
                'choices' => [
                    'Technical' => 'Technical',
                    'Soft' => 'Soft',
                    'Language' => 'Language',
                    'Certification' => 'Certification',
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
            ->add('level', ChoiceType::class, [
                'label' => 'Proficiency Level',
                'choices' => [
                    'Beginner' => 'Beginner',
                    'Intermediate' => 'Intermediate',
                    'Advanced' => 'Advanced',
                    'Expert' => 'Expert',
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
            ->add('yearsOfExperience', IntegerType::class, [
                'label' => 'Years of Experience',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'e.g., 3',
                    'min' => '0',
                    'max' => '50',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description (optional)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Describe your experience with this skill, projects you\'ve used it on, etc.'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Skill::class,
        ]);
    }
}
