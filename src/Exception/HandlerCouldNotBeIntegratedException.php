<?php
namespace Monty\Exception;

/**
 * Class HandlerCouldNotBeIntegratedException
 * @package Monty\Exception
 * @author Willi Eßer <willi.esser@troublete.com>
 * @copyright 2017 Willi Eßer
 */
class HandlerCouldNotBeIntegratedException extends GeneralException
{
    /**
     * HandlerCouldNotBeIntegratedException constructor.
     * @param array ...$parameters
     */
    public function __construct(...$parameters)
    {
        parent::__construct(...$parameters);
        $this->message = 'The lifecycle position where the handlers should be applied does not exist.';
    }
}