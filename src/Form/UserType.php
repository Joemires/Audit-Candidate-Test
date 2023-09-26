<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(), new Email()
                ]
            ])
            ->add('country', CountryType::class, [
                'required' => true,
                // 'alpha3' => true,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('timezone', TimezoneType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('password', PasswordType::class, [
                'required' => true,
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'constraints' => [
                new UniqueEntity(fields: ['email']),
            ],
        ]);
    }

    public function getName()
    {
        return 'create_user';
    }
}