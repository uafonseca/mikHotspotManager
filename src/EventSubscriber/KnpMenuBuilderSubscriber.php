<?php
// src/EventSubscriber/KnpMenuBuilderSubscriber.php
namespace App\EventSubscriber;

use App\Services\RouterosService;
use KevinPapst\AdminLTEBundle\Event\KnpMenuEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

class KnpMenuBuilderSubscriber implements EventSubscriberInterface
{
    private Security $security;

    private RouterosService $service;

    /**
     * Undocumented function
     *
     * @param Security $security
     * @param RouterosService $service
     */
    public function __construct(Security $security, RouterosService $service)
    {
        $this->security = $security;
        $this->service = $service;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KnpMenuEvent::class => ['onSetupMenu', 100],
        ];
    }
    
    public function onSetupMenu(KnpMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild('MainNavigationMenuItem', [
               'label' => '',
            'childOptions' => $event->getChildOptions()
        ])->setAttribute('class', 'header');
        
        $menu->addChild('dashboard', [
            'route' => 'home',
            'label' => 'DASHBOARD',
            'childOptions' => $event->getChildOptions(),
            // 'extras' => [
            //     'badge' => [
            //         'color' => 'yellow',
            //         'value' => 4,
            //     ],
            // ],
        ])->setLabelAttribute('icon', 'fas fa-tachometer-alt');

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $menu->addChild('router', [
            'route' => 'router_new',
            'label' => 'CONFIGURACIÓN',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fas fa-cog');
        }
        $menu->addChild('users', [
            'label' => 'CLIENTES',
            'route' => 'user_new',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fas fa-users');

        $menu->getChild('users')->addChild('users_add', [
            'label' => 'ADICIONAR',
            'route' => 'user_new',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fas fa-user-plus');

        $menu->getChild('users')->addChild('users_list', [
            'label' => 'LISTADO',
            'route' => 'user_index',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fas fa-list');
        
        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $menu->addChild('profile', [
                'label' => 'PERFILES',
                'route' => 'user_profile_index',
                'childOptions' => $event->getChildOptions(),
            ])->setLabelAttribute('icon', 'fas  fa-id-card');
    
            $menu->getChild('profile')->addChild('user_profile_new', [
                'label' => 'ADICIONAR',
                'route' => 'user_profile_new',
                'childOptions' => $event->getChildOptions(),
            ])->setLabelAttribute('icon', 'fas fa-plus');
            
            $menu->getChild('profile')->addChild('user_profile_index', [
                'label' => 'LISTADO',
                'route' => 'user_profile_index',
                'childOptions' => $event->getChildOptions(),
            ])->setLabelAttribute('icon', 'fas fa-list');
        }

        $menu->addChild('hosts', [
            'label' => 'Hosts',
            'route' => 'routerOs-hosts',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fa fa-laptop');

        $menu->addChild('dhcp', [
            'label' => 'DHCP Leases',
            'route' => 'routerOs-dhcp',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fa fa-network-wired');

        $menu->addChild('debts', [
            'label' => 'Deudas',
            'route' => 'debts',
            'childOptions' => $event->getChildOptions(),
               'extras' => [
                'badge' => [
                    'color' => $this->service->countDebs() > 0 ? 'red' : 'green',
                    'value' => $this->service->countDebs(),
                ],
            ],
        ])->setLabelAttribute('icon', 'fa fa-exclamation-triangle');

        $menu->addChild('logout', [
            'label' => 'Salir',
            'route' => 'app_logout',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fa fa-arrow-left');
    }
}
