<?php

require_once __DIR__ . '/../vendor/autoload.php';

(function() {
    $resources = new \App\ResourceManager();
    $emitter = new Zend\Diactoros\Response\SapiEmitter();
    try {
        $dispatcher = $resources->getDispatcher();
        $request = \Zend\Diactoros\ServerRequestFactory::fromGlobals();
        $response = $dispatcher->dispatch($request);
        $emitter->emit($response);
    } catch (\Throwable $e) {
        $response = (new \Zend\Diactoros\Response\HtmlResponse($resources->getTemplateEngine()->render('http-error/500', ['details' => $e->getMessage()])))
            ->withStatus(500, "Internal Server Error");
        $emitter->emit($response);
    }
})();
