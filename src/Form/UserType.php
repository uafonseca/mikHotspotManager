<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\UserProfile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, [
                'label' => 'Usuario'
            ])
        
            ->add('plainPassword',null, [
                'label' => 'Contraseña'
            ])

            ->add('isLocal', CheckboxType::class,[
                'label'=>'Acceso a la plataforma',
                'required' => false
            ])

            ->add('time',null,[
                'mapped' => false,
                'label' => 'Límite de tiempo',
            ])

            ->add('comment',null,[
                'mapped' => false,
                'label' => 'Comentario',
                'attr' => [
                    // 'class' => 'hidden'
                ]
            ])

            ->add('profile',EntityType::class,[
                'label' => 'Perfil',
                'class' => UserProfile::class,
                'attr' => [
                    // 'class' => 'hidden'
                ]
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
