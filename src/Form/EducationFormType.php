<?php

namespace App\Form;

use App\Entity\Education;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EducationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentYear = (int) date('Y');
        $years = range($currentYear + 5, 1950);
        $yearChoices = array_combine($years, $years);

        $builder
            ->add('institution', TextType::class, [
                'label' => 'University/Institution',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'e.g., Harvard University, MIT, Stanford'
                ],
            ])
            ->add('degree', ChoiceType::class, [
                'label' => 'Degree Type',
                'choices' => [
                    'High School Diploma' => 'High School Diploma',
                    'Associate Degree' => 'Associate Degree',
                    'Bachelor\'s Degree' => 'Bachelor\'s Degree',
                    'Master\'s Degree' => 'Master\'s Degree',
                    'PhD/Doctorate' => 'PhD/Doctorate',
                    'Engineering Degree' => 'Engineering Degree',
                    'Medical Degree' => 'Medical Degree',
                    'Law Degree' => 'Law Degree',
                    'MBA' => 'MBA',
                    'Certificate' => 'Certificate',
                    'Diploma' => 'Diploma',
                    'Other' => 'Other',
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
            ->add('fieldOfStudy', TextType::class, [
                'label' => 'Field of Study/Specialty',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'e.g., Computer Science, Business Administration, Mechanical Engineering'
                ],
            ])
            ->add('startYear', ChoiceType::class, [
                'label' => 'Start Year',
                'choices' => $yearChoices,
                'required' => false,
                'placeholder' => 'Select year',
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
            ->add('endYear', ChoiceType::class, [
                'label' => 'End Year',
                'choices' => $yearChoices,
                'required' => false,
                'placeholder' => 'Select year',
                'attr' => [
                    'class' => 'form-select',
                ],
            ])
            ->add('isCurrentlyStudying', CheckboxType::class, [
                'label' => 'I am currently studying here',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ])
            ->add('gpa', NumberType::class, [
                'label' => 'GPA (optional)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'e.g., 3.8',
                    'step' => '0.01',
                    'min' => '0',
                    'max' => '4',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description (optional)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Describe your studies, achievements, relevant coursework, etc.'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Education::class,
        ]);
    }
}
