<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class Application
{
    /** @var ServiceContainer */
    private $container;

    public function __construct()
    {
        $this->container = new ServiceContainer();
    }

    public function run()
    {
        $request = Request::createFromGlobals();

        $context = new RequestContext();
        $context->fromRequest($request);

        $matcher = new UrlMatcher(Routes::getRoutes(), $context);

        try {
            $response = $this->handleRequest($request, $matcher);
        } catch (ResourceNotFoundException $e) {
            $response = new JsonResponse(['error' => 'Not found'], 404);
        } catch (\Throwable $e) {
            $response = new JsonResponse(['error' => 'Something went wrong'], 500);
        }

        $response->send();
    }

    private function handleRequest(Request $request, UrlMatcher $matcher)
    {
        $parameters = $matcher->match($request->getPathInfo());
        $request->attributes->add($parameters);

        $controllerClass = $parameters['_controller'];
        $action = $parameters['_action'];

        $controller = $this->container[$controllerClass];

        return $controller->$action($request);
    }
}
