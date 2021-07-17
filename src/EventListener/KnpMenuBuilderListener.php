<?php
// src/EventListener/KnpMenuBuilderListener.php
namespace App\EventListener;

use KevinPapst\AdminLTEBundle\Event\KnpMenuEvent;

class KnpMenuBuilderListener
{
    public function onSetupMenu(KnpMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild('MainNavigationMenuItem', [
       	    'label' => 'MAIN NAVIGATION',
            'childOptions' => $event->getChildOptions()
        ])->setAttribute('class', 'header');
        
        $menu->addChild('blogId', [
            'route' => 'item_symfony_route',
            'label' => 'Blog',
            'childOptions' => $event->getChildOptions()
        ])->setLabelAttribute('icon', 'fas fa-tachometer-alt');
        
        $menu->getChild('blogId')->addChild('ChildOneItemId', [
            'route' => 'child_1_route',
            'label' => 'ChildOneDisplayName',
            'childOptions' => $event->getChildOptions()
        ])->setLabelAttribute('icon', 'fas fa-rss-square');
        
        $menu->getChild('blogId')->addChild('ChildTwoItemId', [
            'route' => 'child_2_route',
            'label' => 'ChildTwoDisplayName',
            'childOptions' => $event->getChildOptions()
        ]);
    }
}