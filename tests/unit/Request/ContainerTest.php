<?php
namespace Request;

use Monty\Exception\PropertyCouldNotBeSetException;
use Monty\Exception\PropertyNotAvailableException;
use Monty\Request;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class ContainerTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Request
     */
    protected $request;

    protected function _before()
    {
        $this->request = new Request(
            \Symfony\Component\HttpFoundation\Request::create('http://localhost/index/123/other')
        );
    }

    public function testSetContainerValuesPositive()
    {
        $this->request->set('object', new \stdClass());
        $this->request->set('value', '123');

        $this->tester->assertInstanceOf(\stdClass::class, $this->request->getRawRequest()->attributes->get('object'));
        $this->tester->assertEquals('123', $this->request->getRawRequest()->attributes->get('value'));
    }

    public function testSetPropertyArrayNegative()
    {
        $request = $this->request;
        $this->tester->expectException(
            PropertyCouldNotBeSetException::class,
            function () use ($request) {
                $request->set('null', []);
            }
        );
    }

    public function testSetPropertyNullNegative()
    {
        $request = $this->request;
        $this->tester->expectException(
            PropertyCouldNotBeSetException::class,
            function () use ($request) {
                $request->set('null', null);
            }
        );
    }

    public function testSetPropertyWrongIdNegative()
    {
        $request = $this->request;
        $this->tester->expectException(
            PropertyCouldNotBeSetException::class,
            function () use ($request) {
                $request->set(123123, null);
            }
        );
    }

    public function testGetResponseObject()
    {
        $this->request->set('object', new \stdClass());
        $this->tester->assertInstanceOf(\stdClass::class, $this->request->get('object'));
    }

    public function testGetProperty()
    {
        $this->request->set('value', '123');
        $this->tester->assertEquals('123', $this->request->get('value'));
    }

    public function testGetWrongKeyNegative()
    {
        $request = $this->request;
        $this->tester->expectException(
            PropertyNotAvailableException::class,
            function () use ($request) {
                $this->request->get(123123);
            }
        );
    }
}