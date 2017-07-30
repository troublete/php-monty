<?php
namespace Monty;

/**
 * Interface ResponseInterface
 * @package Monty
 * @author Willi Eßer <willi.esser@troublete.com>
 * @copyright 2017 Willi Eßer
 */
interface ResponseInterface
{
    /**
     * Method to echo/render the response of a response option
     * @return mixed
     */
    public function send();
}