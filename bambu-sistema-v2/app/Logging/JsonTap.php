<?php

namespace App\Logging;

use Monolog\Formatter\JsonFormatter;
use Illuminate\Log\Logger;

class JsonTap
{
    public function __invoke(Logger $logger)
    {
        foreach ($logger->getLogger()->getHandlers() as $handler) {
            $handler->setFormatter(new JsonFormatter());
        }
    }
}