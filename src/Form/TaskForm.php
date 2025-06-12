<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isAdmin = $options['is_admin'] ?? false;

        $builder
            ->add('title', TextType::class, [
                'attr' => ['placeholder' => 'Enter task title'],
                'help' => 'Task title should be between 3 and 50 characters'
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['rows' => 4, 'placeholder' => 'Enter task description'],
                'help' => 'Provide a detailed description of the task'
            ])
            ->add('finished', CheckboxType::class, [
                'required' => false,
                'label' => 'Mark as completed'
            ])
            ->add('dueDate', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'label' => 'Due Date'
            ]);

        // Only add assignee fields for admin users
        if ($isAdmin) {
            $builder
                ->add('assigneeName', TextType::class, [
                    'attr' => ['placeholder' => 'Enter assignee name'],
                    'required' => false,
                    'label' => 'Assignee Name',
                    'help' => 'Optional: Name of the person this task is assigned to'
                ])
                ->add('assigneeEmail', EmailType::class, [
                    'attr' => ['placeholder' => 'Enter assignee email'],
                    'required' => false,
                    'label' => 'Assignee Email',
                    'help' => 'Optional: Email of the person this task is assigned to'
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'is_admin' => false,
        ]);

        $resolver->setAllowedTypes('is_admin', 'bool');
    }
}
