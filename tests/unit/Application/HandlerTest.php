<?php
namespace Application;


use Codeception\Util\Stub;
use Monty\Application;
use Monty\Request;
use Monty\Response;
use Monty\RouteHandler;

class HandlerTest extends \Codeception\Test\Unit
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

    public function testGetMethodRequest()
    {
        $tester = $this->tester;

        $this->app->get(
            '/route/{routeId:\d+}/{otherParam}',
            function (Request $request, Response $response, $routeId, $otherParam) use ($tester) {
                $tester->assertEquals(123, $routeId);
                $tester->assertEquals('stuff', $otherParam);
                $tester->assertEquals(123, $request->parameters()->get('routeId'));
                $tester->assertEquals('stuff', $request->parameters()->get('otherParam'));
                $response = new Response('GET');
                return new Response();
            }
        );
    }

    public function testGetMethodNegativeRequest()
    {
        $response = null;

        $this->app->post(
            '/route/{routeId:\d+}/{otherParam}',
            function (Request $request, Response $response, $routeId, $otherParam) use (&$response) {
                $response = new Response('POST');
                return new Response();
            }
        );

        $this->tester->assertNull($response);
    }

    public function testGetMethodRequestMultipleRoutes()
    {
        /** @var Response $response */
        $response = null;

        $this->app->get(
            '/another',
            function (Request $request, Response $response, $routeId, $otherParam) use (&$response) {
                $response = new Response('second');
                return new Response();
            }
        );

        $this->tester->assertNull($response);

        $this->app->get(
            '/route/{routeId:\d+}/{otherParam}',
            function (Request $request, Response $response, $routeId, $otherParam) use (&$response) {
                $response = new Response('first');
                return new Response();
            }
        );

        $this->tester->assertEquals('first', $response->getContent());
    }
}