<?php

namespace App\Web\Actions\ProcessList;

use App\Web\Actions\AbstractAction;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class ListAction extends AbstractAction
{
    /**
     * @param ServerRequest $request
     * @return Response\HtmlResponse
     */
    public function __invoke(ServerRequest $request)
    {
        $formData = [
            'user' => $_GET['user'] ?? '',
            'host' => $_GET['host'] ?? '',
            'db' => $_GET['db'] ?? '',
            'show_inactive' => !empty($_GET['show_inactive']),
        ];
        $mysqlServerName = $this->resources->getConfig()->get('application.mysql-server-name');
        $hideCurrentProcess = $this->resources->getConfig()->get('application.hide-current-process');

        $pdo = $this->resources->getPdo();

        $stmt = $pdo->query('SELECT CONNECTION_ID()');
        $currentProcessId = $stmt->fetchColumn();

        $stmt = $pdo->query('SHOW PROCESSLIST');
        $processes = [];
        while (($process = $stmt->fetch(\PDO::FETCH_ASSOC)) !== false) {
            if ($formData['user'] !== '' && stripos($process['User'], $formData['user']) === false) {
                continue;
            }
            if ($formData['host'] !== '' && stripos($process['Host'], $formData['host']) === false) {
                continue;
            }
            if ($formData['db'] !== '' && stripos($process['db'], $formData['db']) === false) {
                continue;
            }
            if (!$formData['show_inactive'] && $process['Info'] === NULL) {
                continue;
            }
            if ($hideCurrentProcess && $process['Id'] == $currentProcessId) {
                continue;
            }
            $processes[] = $process;
        }

        $response = new Response\HtmlResponse(
            $this->resources->getTemplateEngine()->render('process-list/list', [
                'processes' => $processes,
                'mysqlServerName' => $mysqlServerName,
                'formData' => $formData,
            ])
        );
        return $response;
    }

}
