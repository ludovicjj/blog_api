<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;

class SetIsMeOnCurrentUserSubscriber implements EventSubscriberInterface
{
    public function __construct(private Security $security)
    {
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        /** @var User|null $user */
        $user = $this->security->getUser();

        if (!$user) {
            return;
        }
        // Get access to item or items for current API request
        // https://symfonycasts.com/screencast/api-platform-extending/listeners-data
        // $data = $event->getRequest()->attributes->get('data');

        $user->setIsMe(true);
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}
