<?php

namespace App\Form;

use App\Entity\UserProfile;
use App\Services\api;
use App\Services\RouterosService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileType extends AbstractType
{
    private $api;

    public function __construct(RouterosService $api)
    {
        $this->api = $api;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre del perfil'
            ])
            ->add('addresPool', ChoiceType::class, [
                'label' => 'Address poll',
                'choices' => $this->getAddressPolls(),
                'required' => true
            ])
            ->add('rateLimit', null, [
                'label' => 'límite de datos',
                'required' => false
            ])
            ->add('price', NumberType::class, [
                'label' => 'Precio',
                'required' => true
            ])
            ->add('roles',ChoiceType::class,[
                'label' => 'Roles',
                'multiple' => true,
                'choices' => [
                    'ROLE_USER'=> 'ROLE_USER',
                    'ROLE_SUPER_ADMIN' =>'ROLE_SUPER_ADMIN'
                ]
            ])
        ;
    }

    private function getAddressPolls()
    {
        $pools = $this->api->comm("/ip/pool/print");
            $poolss = [];
            foreach ($pools as $pool) {
                if($pool['name'] != '')
                    $poolss[$pool['name']] =$pool['name'];
            }
            return $poolss;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserProfile::class,
        ]);
    }
}
