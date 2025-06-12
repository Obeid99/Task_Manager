<?php

namespace App\Form;

use App\Entity\WorkExperience;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WorkExperienceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('company', TextType::class, [
                'label' => 'Company Name',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'e.g., Google, Microsoft, Apple'
                ],
            ])
            ->add('position', TextType::class, [
                'label' => 'Job Title/Position',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'e.g., Software Engineer, Project Manager, Marketing Specialist'
                ],
            ])
            ->add('employmentType', ChoiceType::class, [
                'label' => 'Employment Type',
                'choices' => [
                    'Full-time' => 'Full-time',
                    'Part-time' => 'Part-time',
                    'Contract' => 'Contract',
                    'Internship' => 'Internship',
                    'Freelance' => 'Freelance',
                    'Temporary' => 'Temporary',
                    'Volunteer' => 'Volunteer',
                ],
                'required' => false,
                'placeholder' => 'Select employment type',
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
            ->add('location', TextType::class, [
                'label' => 'Location',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'e.g., New York, NY or Remote'
                ],
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Start Date',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('endDate', DateType::class, [
                'label' => 'End Date',
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('isCurrentPosition', CheckboxType::class, [
                'label' => 'I currently work here',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Job Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Describe your responsibilities, achievements, and key projects...'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WorkExperience::class,
        ]);
    }
}
