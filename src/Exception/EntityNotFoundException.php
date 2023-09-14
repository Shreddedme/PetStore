<?php

namespace App\Exception;

use Exception;

class EntityNotFoundException extends Exception
{
    public function __construct(string $entityName, int $id, $code = 404)
    {
        $message = "Entity '$entityName' with ID '$id' not found.";
        parent::__construct($message);
    }
}