<?php

namespace App\Form;

use App\Entity\Router;
use App\Services\RouterosService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RouterType extends AbstractType
{
    private $api;
    
    public function __construct(RouterosService $api)
    {
        $this->api = $api;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('routerName', null, ['label' => 'Nombre del router'])
            ->add('ip', null, ['label' => 'IP'])
            ->add('username', null, ['label' => 'Usuario'])
            ->add('password', null, ['label' => 'Contraseña'])
            ->add('interface', ChoiceType::class, [
                'label' => 'Interfáz a monitorear',
                'choices' => $this->getInterfaces(),
                'required' => false
                ])
        ;
    }

    public function getInterfaces()
    {
        $interfaces = $this->api->comm("/interface/print");
        $interfacess = [];
        foreach ($interfaces as $interface) {
            if ($interface['name'] != '') {
                $interfacess[$interface['name']] =$interface['name'];
            }
        }
        return $interfacess;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Router::class,
        ]);
    }
}
