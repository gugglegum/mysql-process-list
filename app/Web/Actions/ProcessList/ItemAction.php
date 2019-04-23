<?php

namespace App\Web\Actions\ProcessList;

use App\Web\Actions\AbstractAction;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class ItemAction extends AbstractAction
{
    /**
     * @param ServerRequest $request
     * @return Response\HtmlResponse
     */
    public function __invoke(ServerRequest $request)
    {
        $processId = (int) $request->getAttribute('id');
        $mysqlServerName = $this->resources->getConfig()->get('application.mysql-server-name');

        $pdo = $this->resources->getPdo();
        $stmt = $pdo->query('SHOW FULL PROCESSLIST');

        $process = null;
        while (($process = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
            if ((int) $process['Id'] === $processId) {
                break;
            }
        }

        $response = new Response\HtmlResponse(
            $this->resources->getTemplateEngine()->render('process-list/item', [
                'processId' => $processId,
                'process' => $process,
                'mysqlServerName' => $mysqlServerName,
            ])
        );
        return $response;
    }

}
