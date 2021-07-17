<?php

namespace App\Form;

use App\Entity\Router;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RouterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('routerName', null, ['label' => 'Nombre del router'])
            ->add('ip', null, ['label' => 'IP'])
            ->add('username', null, ['label' => 'Usuario'])
            ->add('password', null, ['label' => 'Contraseña'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Router::class,
        ]);
    }
}
