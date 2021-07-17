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
            'label' => 'Dashboard',
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
            'label' => 'Router',
            'childOptions' => $event->getChildOptions(),
            // 'extras' => [
            //     'badge' => [
            //         'color' => 'yellow',
            //         'value' => 4,
            //     ],
            // ],
        ])->setLabelAttribute('icon', 'fas fa-wifi');
        
       
    }
}