<?php
namespace Application;


use Codeception\Util\Stub;
use Monty\Application;
use Monty\Exception\HandlerCouldNotBeIntegratedException;
use Monty\Request;
use Monty\Response;
use Monty\RouteHandler;

class MiddlewareTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Application
     */
    protected $app;

    protected function _before()
    {
        $httpRequest = \Symfony\Component\HttpFoundation\Request::create('http://localhost/route/123/stuff');

        $app = Stub::make(
            Application::class,
            [
                'request' => new Request($httpRequest),
                'routeHandler' => new RouteHandler(),
                'response' => new Response('Not')
            ]
        );

        $this->app = $app;
    }

    public function testPrependMiddleware()
    {
        $tester = $this->tester;

        $this->app->before(
            function ($req, $res) {
                return new Response('prepended');
            }
        );

        $this->app->get(
            '/route/{int}/{string}',
            function ($req, $res) use ($tester) {
                $tester->assertEquals('prepended', $res->getContent());
                return new Response('first');
            },
            function ($req, $res) use ($tester) {
                $tester->assertEquals('prepended', $res->getContent());
            }
        );
    }

    public function testAppendMiddleware()
    {
        $tester = $this->tester;

        $this->app->after(
            function ($req, $res) use ($tester) {
                $tester->assertEquals('first', $res->getContent());
                return new Response('appended');
            },
            function ($req, $res) use ($tester) {
                $tester->assertEquals('first', $res->getContent());
            }
        );

        $this->app->get(
            '/route/{int}/{string}',
            function ($req, $res) use ($tester) {
                return new Response('first');
            }
        );
    }

    public function testMiddlewareApplyNegative()
    {
        $app = $this->app;
        $this->tester->expectException(
            HandlerCouldNotBeIntegratedException::class,
            function () use ($app) {
                $app->middleware(
                    3,
                    function () {}
                );
            }
        );
    }
}