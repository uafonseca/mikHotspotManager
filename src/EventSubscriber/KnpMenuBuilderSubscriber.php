<?php
// src/EventSubscriber/KnpMenuBuilderSubscriber.php
namespace App\EventSubscriber;

use KevinPapst\AdminLTEBundle\Event\KnpMenuEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class KnpMenuBuilderSubscriber implements EventSubscriberInterface
{

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

        $menu->addChild('router', [
            'route' => 'router_new',
            'label' => 'CONFIGURACIÓN',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fas fa-cog');

        $menu->addChild('users',[
            'label' => 'CLIENTES',
            'route' => 'user_new',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fas fa-users');

        $menu->getChild('users')->addChild('users_add',[
            'label' => 'ADICIONAR',
            'route' => 'user_new',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fas fa-user-plus');

        $menu->getChild('users')->addChild('users_list',[
            'label' => 'LISTADO',
            'route' => 'user_index',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fas fa-list');
        
        $menu->addChild('profile',[
            'label' => 'PERFILES',
            'route' => 'user_profile_index',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fas  fa-id-card');

        $menu->getChild('profile')->addChild('user_profile_new',[
            'label' => 'ADICIONAR',
            'route' => 'user_profile_new',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fas fa-plus');
        
        $menu->getChild('profile')->addChild('user_profile_index',[
            'label' => 'LISTADO',
            'route' => 'user_profile_index',
            'childOptions' => $event->getChildOptions(),
        ])->setLabelAttribute('icon', 'fas fa-list');

        
    }
}