<?php

namespace Bosunski\LaravelIot\Foundation;

use Exception;
use Illuminate\Support\Collection;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Throwable;

class Mqtt
{
    protected $controllerNamespace = '\\App\\Mqtt\\Controllers\\';

    private $topics = [];

    /**
     * @var RouteCollection
     */
    private $routes;

    /**
     * @var RequestContext
     */
    private $routeContext;

    /**
     * @var UrlMatcher
     */
    private $urlMatcher;

    public function __construct()
    {
        $this->routes = new RouteCollection();
        $this->routeContext = new RequestContext("/");
        $this->urlMatcher = new UrlMatcher($this->routes, $this->routeContext);
    }

    public function topic($identifier, $handler)
    {
        $this->topics[] = [
            'route' => $identifier,
            'handler' => $handler,
        ];

        $route = new Route($identifier, ['handler' => $handler]);

        $this->routes->add($identifier, $route);
    }

    public function handle($mqtt, $input, $payload)
    {
        try {
            $topicData = Collection::make($this->urlMatcher->match($input));
            $params = $topicData->except(['_route', 'handler']);
        } catch (ResourceNotFoundException $e) {
            dump($e->getMessage());

            return false;
        };

        $params = array_merge($params->toArray(), ['payload' => json_decode($payload, true), 'mqtt' => $mqtt]);

        if (is_callable($handler = $topicData->get('handler'))) return call_user_func_array($handler, $params);

        $this->handleControllerCall($handler, $params);
    }

    protected function handleControllerCall($handler, $params)
    {
        list($controller, $method) = explode('@', $handler);

        $fqcn = $this->controllerNamespace . $controller;

        $obj = app()->make($fqcn);

        try {
            return call_user_func_array([$obj, $method], $params);
        } catch (Exception $exception) {
            echo "Error: " . $exception->getMessage();
        } catch (Throwable $throwable) {
            echo "Error: " . $throwable->getMessage();
        }
    }

    public function getTopics()
    {
        return $this->topics;
    }
}
