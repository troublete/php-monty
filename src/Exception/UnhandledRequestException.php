<?php
namespace Monty\Exception;

/**
 * Class UnhandledRequestException
 * @package Monty\Exception
 * @author Willi Eßer <willi.esser@troublete.com>
 * @copyright 2017 Willi Eßer
 */
class UnhandledRequestException extends GeneralException
{
    /**
     * UnhandledRequestException constructor.
     * @param array ...$parameters
     */
    public function __construct(...$parameters)
    {
        parent::__construct(...$parameters);
        $this->message = 'The request sent was never processed';
    }
}