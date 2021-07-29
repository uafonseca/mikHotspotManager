<?php

/*
 * This file is part of the AdminLTE bundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Event;

use App\Entity\Log;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Base event class to make theme related events easier to detect
 */
class LogEvent extends Event
{
    /** @var Log $log */
    private $log;

    public function __construct(Log $log)
    {
        $this->log = $log;
    }

 /**
  * Undocumented function
  *
  * @return Log|null
  */
    public function getLog():?Log
    {
        return $this->log;
    }

    /**
     * Set the value of log
     *
     * @return  self
     */ 
    public function setLog($log)
    {
        $this->log = $log;

        return $this;
    }
}
