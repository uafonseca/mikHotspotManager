<?php

namespace App\Form;

use App\Entity\Investmenst;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvestmenstType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mount',null,[
                'label' => 'Monto de la inversión'
            ])
            ->add('comment', null, [
                'label' => 'Comentario',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Investmenst::class,
        ]);
    }
}
