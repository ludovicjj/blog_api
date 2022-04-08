<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CheeseNotificationRepository;

/**
 * @ORM\Entity(repositoryClass=CheeseNotificationRepository::class)
 */
class CheeseNotification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity=CheeseListing::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private CheeseListing $cheeseListing;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $notificationText;

    public function __construct(CheeseListing $cheeseListing, string $notificationText)
    {
        $this->cheeseListing = $cheeseListing;
        $this->notificationText = $notificationText;
    }
}