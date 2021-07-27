<?php

namespace App\Form;

use App\Entity\Router;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Services\RouterosService;
use Doctrine\ORM\EntityRepository;
use DoctrineExtensions\Query\Mysql\Regexp;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Range;

class UserType extends AbstractType
{

    private $security;
    private $api;
    public function __construct(Security $security, RouterosService $api)
    {
        $this->security = $security;
        $this->api = $api;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('macAddress', null, [
                'label' => 'Dirección MAC',
                'required' => $this->api->getRouter()->getHotspotloginType() === Router::LOGIN_TYPE_MAC_AS_USER_AND_PASS,
               'attr' => ['placeholder' => '00:00:00:00:00:00','style'=>'font-size:20px']
            ])
            ->add('username', null, [
                'label' => 'Usuario'
            ])
        
            ->add('plainPassword',null, [
                'label' => 'Contraseña',
                'required' => true
            ])

            ->add('isLocal', CheckboxType::class,[
                'label'=>'Acceso a la plataforma',
                'required' => false
            ])

           

            ->add('comment',null,[
                'mapped' => false,
                'label' => 'Comentario',
            ])

            ->add('profile',EntityType::class,[
                'label' => 'Perfil',
                'class' => UserProfile::class,
                'query_builder' => function(EntityRepository $e){
                    if($this->security->isGranted('ROLE_SUPER_ADMIN'))
                        return $e->createQuerybuilder('p');
                    return $e->createQuerybuilder('p')
                        ->where('p.roles LIKE :r')
                        ->setParameter('r','%ROLE_USER%')
                        
                    ;
                }
            ])   
        ;
        if($this->security->isGranted('ROLE_SUPER_ADMIN')){
            $builder->add('time', IntegerType::class,[
                'mapped' => false,
                'label' => 'Crédito inicial',
            ]);
        }else{
            $builder->add('time', IntegerType::class,[
                'mapped' => false,
                'label' => 'Crédito inicial',
                'constraints' => [
                    new Range([
                        'min' => 1,
                        
                    ])
                ]
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
