<?php

namespace App\Web\Components\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class UrlFromRouteExtension implements ExtensionInterface
{
    /**
     * @var \Aura\Router\RouterContainer
     */
    private $webRouter;

    /**
     * UrlFromRouteExtension constructor.
     * @param \Aura\Router\RouterContainer $webRouter
     */
    public function __construct(\Aura\Router\RouterContainer $webRouter)
    {
        $this->webRouter = $webRouter;
    }

    /**
     * @param Engine $engine
     */
    public function register(Engine $engine)
    {
        $engine->registerFunction('urlFromRoute', [$this, 'urlFromRoute']);
    }

    /**
     * @param string $routeName
     * @param array $data
     * @param array $queryData
     * @return string
     * @throws \Aura\Router\Exception\RouteNotFound
     */
    public function urlFromRoute(string $routeName, array $data = [], array $queryData = []): string
    {
        $url = $this->webRouter->getGenerator()->generate($routeName, $data);
        if (count($queryData) != 0) {
            $url .= '?' . http_build_query($queryData);
        }
        return $url;
    }

    /**
     * @param string $routeName
     * @param array $data
     * @param array $queryData
     * @return string
     * @throws \Aura\Router\Exception\RouteNotFound
     */
    public function urlFromRouteRaw(string $routeName, array $data = [], array $queryData = []): string
    {
        $rawUrl = $this->webRouter->getGenerator()->generateRaw($routeName, $data);
        if (count($queryData) != 0) {
            $rawUrl .= '?' . http_build_query($queryData);
        }
        return $rawUrl;
    }
}
