<?php

namespace App\Logging;

use Monolog\Formatter\JsonFormatter;
use Illuminate\Log\Logger;

class ApiAccessTap
{
    public function __invoke(Logger $logger)
    {
        foreach ($logger->getLogger()->getHandlers() as $handler) {
            $handler->setFormatter(new JsonFormatter(JsonFormatter::BATCH_MODE_JSON, false));
        }
    }
}