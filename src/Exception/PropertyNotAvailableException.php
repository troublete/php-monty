<?php
namespace Monty\Exception;

/**
 * Class PropertyNotAvailableException
 * @package Monty\Exception
 */
class PropertyNotAvailableException extends GeneralException
{
    /**
     * PropertyNotAvailableException constructor.
     * @param array ...$parameters
     */
    public function __construct(...$parameters)
    {
        parent::__construct(...$parameters);
        $this->message = 'Property not available on the request.';
    }
}