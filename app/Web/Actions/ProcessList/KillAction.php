<?php

namespace App\Web\Actions\ProcessList;

use App\Web\Actions\AbstractAction;
use Aura\Router\Exception\RouteNotFound;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class KillAction extends AbstractAction
{
    /**
     * @param ServerRequest $request
     * @return Response\RedirectResponse
     * @throws RouteNotFound
     */
    public function __invoke(ServerRequest $request)
    {
        $processId = (int) $request->getAttribute('id');

        $pdo = $this->resources->getPdo();
        $pdo->exec("KILL {$processId}");

        $response = new Response\RedirectResponse($this->resources->getWebRouter()->getGenerator()->generate('process-list') . '?' . http_build_query($_GET));
        return $response;
    }

}
