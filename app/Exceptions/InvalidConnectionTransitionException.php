<?php

namespace App\Exceptions;

use App\Enums\ConnectionStatus;
use RuntimeException;

/**
 * Thrown when a connection status transition is not allowed.
 */
class InvalidConnectionTransitionException extends RuntimeException
{
    public function __construct(ConnectionStatus $from, ConnectionStatus $to)
    {
        parent::__construct("Cannot transition connection from '{$from->value}' to '{$to->value}'.");
    }
}
