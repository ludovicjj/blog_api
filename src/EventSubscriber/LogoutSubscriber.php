<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSubscriber implements EventSubscriberInterface
{
    public function onLogoutEvent(LogoutEvent $event)
    {
        $contentTypes = $event->getRequest()->getAcceptableContentTypes();

        // let have some fun
        $data = [
            'message' => "See you next time space cowboy"
        ];

        if (in_array('application/json', $contentTypes)) {
            $event->setResponse(new JsonResponse($data, 200));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            LogoutEvent::class => 'onLogoutEvent',
        ];
    }
}
