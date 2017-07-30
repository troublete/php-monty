<?php
namespace Application;


use Codeception\Util\Stub;
use Monty\Application;
use Monty\Exception\UnhandledRequestException;
use Monty\Request;
use Monty\Response;
use Monty\RouteHandler;

class ResponseTest extends \Codeception\Test\Unit
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

    public function testDefaultResponse()
    {
        $this->tester->assertEquals('Not', $this->app->getResponse()->getContent());
    }

    public function testSingleActionPositive()
    {
        $response = null;
        $tester = $this->tester;

        $this->app->get(
            '/route/{id:\d+}/{other}',
            function ($req, Response $res) use (&$response, $tester) {
                $tester->assertEquals($res->getContent(), 'Not');

                $response = new Response('Another');
                return $response;
            }
        );

        $this->tester->assertEquals($response->getContent(), 'Another');
    }

    public function testSingleActionClassPositive()
    {
        require_once __DIR__ . '/Handler.php';

        $this->app->get(
            '/route/{id:\d+}/{other}',
            \Handler::class
        );

        $this->tester->assertEquals($this->app->getResponse()->getContent(), 'Class');
    }

    public function testSingleActionNegative()
    {
        $app = $this->app;

        $this->tester->expectException(
            UnhandledRequestException::class,
            function () use ($app) {
                $this->app->get(
                    '/route/{id:\d+}/{other}',
                    function () {
                    }
                );
            }
        );
    }

    public function testMultipleActionPositive()
    {
        $response = null;
        $tester = $this->tester;

        $this->app->get(
            '/route/{id:\d+}/{other}',
            function ($req, Response $res) use (&$response, $tester) {
                $tester->assertEquals($res->getContent(), 'Not');

                $response = new Response('Another');
                return $response;
            },
            function ($req, $res) {

            }
        );

        $this->tester->assertEquals($response->getContent(), 'Another');
    }

    public function testMultipleActionNegative()
    {
        $app = $this->app;

        $this->tester->expectException(
            UnhandledRequestException::class,
            function () use ($app) {
                $this->app->get(
                    '/route/{id:\d+}/{other}',
                    function () {
                    },
                    function () {

                    }
                );
            }
        );
    }

    public function testNotOverwriteResponse()
    {
        $tester = $this->tester;

        $this->app->get(
            '/route/{id:\d+}/{other}',
            function ($req, Response $res) {
                return new Response('first');
            },
            function ($req, $res) {
                return new Response('second');
            },
            function ($req, $res) use ($tester) {
                $tester->assertEquals($res->getContent(), 'first');
            }
        );
    }

    public function testAccessingPreviousReturn()
    {
        $tester = $this->tester;

        $this->app->get(
            '/route/{id:\d+}/{other}',
            function ($req, Response $res) {
                return new Response('first');
            },
            function (Request $req, $res) use ($tester) {
                $tester->assertEquals($req->previousReturn()->getContent(), 'first');
                return new Response('second');
            },
            function (Request $req, $res) use ($tester) {
                $tester->assertEquals($req->previousReturn()->getContent(), 'second');
                return new Response('third');
            },
            function (Request $req, $res) use ($tester) {
                $tester->assertEquals($req->previousReturn()->getContent(), 'third');
            }
        );
    }
}