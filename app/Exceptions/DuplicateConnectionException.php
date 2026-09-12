<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when attempting to create a duplicate active connection request.
 */
class DuplicateConnectionException extends RuntimeException
{
    public function __construct(string $message = 'An active connection request already exists between these users.')
    {
        parent::__construct($message);
    }
}
