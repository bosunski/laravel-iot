<?php

namespace Xeviant\LaravelIot\Foundation;

use Exception;
use Illuminate\Support\Collection;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Throwable;
use Xeviant\LaravelIot\Mqtt\Contracts\MQTTClientInterface;

class MqttRouter
{
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
    /**
     * @var MQTTClientInterface
     */
    private $MQTTClient;

    public function __construct(MQTTClientInterface $MQTTClient)
    {
        $this->routes = new RouteCollection();
        $this->routeContext = new RequestContext("/");
        $this->urlMatcher = new UrlMatcher($this->routes, $this->routeContext);
        $this->MQTTClient = $MQTTClient;
    }

    public function topic($identifier, $handler)
    {
        $this->topics[] = [
            'route' => $identifier,
            'handler' => $handler,
        ];

        $route = new Route($identifier, ['handler' => $handler]);

        $this->routes->add($identifier, $route);

        return $route;
    }

    public function handle($topic, $payload = "")
    {
        $topicData = Collection::make($this->urlMatcher->match($topic));
        $params = $topicData->except(['_route', 'handler']);

        $params = array_merge($params->toArray(), ['payload' => json_decode($payload, true), 'mqtt' => $this->MQTTClient]);

        if (is_callable($handler = $topicData->get('handler'))) return call_user_func_array($handler, $params);

        return $this->handleControllerCall($handler, $params);
    }

    protected function handleControllerCall($handler, $params)
    {
        list($controller, $method) = explode('@', $handler);

        $obj = app()->make($controller);

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
