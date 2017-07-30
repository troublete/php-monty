<?php
namespace RouteHandler;


use FastRoute\BadRouteException;
use Monty\RouteHandler;

class RegexCreationTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var RouteHandler
     */
    protected $routeHandler;

    protected function _before()
    {
        $this->routeHandler = new RouteHandler();
    }

    public function testSimplePositiveRegexCreation()
    {
        $regexCollection = $this->routeHandler->parseRoute('/index[/{someId}]');
        $this->tester->assertEquals(
            [
                '@/index/(?<someId>[^/]+)@',
                '@/index@'
            ],
            $regexCollection
        );
    }

    public function testSetMatchPositiveRegexCreation()
    {
        $regexCollection = $this->routeHandler->parseRoute('/index[/{someId:\d+}]');
        $this->tester->assertEquals(
            [
                '@/index/(?<someId>\d+)@',
                '@/index@'
            ],
            $regexCollection
        );
    }

    public function testSetSpecialCasePositiveRegexCreation()
    {
        $regexCollection = $this->routeHandler->parseRoute('/index[/{someId:.+}]');
        $this->tester->assertEquals(
            [
                '@/index/(?<someId>.+)@',
                '@/index@'
            ],
            $regexCollection
        );
    }

    public function testMultiplePositiveRegexCreation()
    {
        $regexCollection = $this->routeHandler->parseRoute('/index/{someId}/{otherId}');
        $this->tester->assertEquals(
            [
                '@/index/(?<someId>[^/]+)/(?<otherId>[^/]+)@'
            ],
            $regexCollection
        );
    }

    public function testNegativeRegexCreation()
    {
        $routeHandler = $this->routeHandler;
        $this->tester->expectException(
            BadRouteException::class,
            function () use ($routeHandler) {
                $routeHandler->parseRoute('/index[/{someId}]/sear[ch]');
            }
        );
    }

    public function testNegativeOptionalRegexCreation()
    {
        $routeHandler = $this->routeHandler;
        $this->tester->expectException(
            BadRouteException::class,
            function () use ($routeHandler) {
                $routeHandler->parseRoute('/index/index[es]/{searchId}');
            }
        );
    }
}