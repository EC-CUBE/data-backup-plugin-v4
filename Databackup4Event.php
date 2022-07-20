<?php

namespace Plugin\Databackup4;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Databackup4Event implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [];
    }
}
