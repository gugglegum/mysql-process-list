<?php

namespace App;

class ResourceManager
{
    /**
     * @var \Luracast\Config\Config
     */
    private $config;

    /**
     * @var \Aura\Router\RouterContainer
     */
    private $router;

    /**
     * @var \PDO
     */
    private $db;

    /**
     * @return \Luracast\Config\Config
     */
    public function getConfig(): \Luracast\Config\Config
    {
        if ($this->config === null) {
            $dotenv = new \Dotenv\Dotenv(__DIR__ . '/..');
            $dotenv->overload();
            $dotenv->required('DB_HOST')->notEmpty();
            $this->config = \Luracast\Config\Config::init(__DIR__ . '/../config');
        }
        return $this->config;
    }

    /**
     * @return \Aura\Router\RouterContainer
     */
    public function getWebRouter(): \Aura\Router\RouterContainer
    {
        if ($this->router === null) {
            $this->router =new \Aura\Router\RouterContainer();
            $map = $this->router->getMap();
            $map->route('process-list', '/', Web\Actions\ProcessList\ListAction::class)->allows(['GET']);
            $map->route('process-item', '/{id}', Web\Actions\ProcessList\ItemAction::class)->allows(['GET']);
            $map->route('process-kill', '/kill/{id}', Web\Actions\ProcessList\KillAction::class)->allows(['POST']);
        }
        return $this->router;
    }

    /**
     * @return \mindplay\middleman\Dispatcher
     */
    public function getDispatcher(): \mindplay\middleman\Dispatcher
    {
        $dispatcher = new \mindplay\middleman\Dispatcher([
            new \Middlewares\AuraRouter($this->getWebRouter()),
            new \Middlewares\RequestHandler(new \Middlewares\Utils\RequestHandlerContainer([$this])),
        ]);
        return $dispatcher;
    }

    /**
     * @param bool $newInstance
     * @return \PDO
     */
    public function getPdo(bool $newInstance = false): \PDO
    {
        $dbConfig = $this->getConfig()->get('database.connections.master');

        if ($this->db === null || $newInstance) {

//            try {
                $db = new \PDO(
                    'mysql:host=' . $dbConfig['host'] . ($dbConfig['port'] ? ';port=' . $dbConfig['port'] : ''),
                    $dbConfig['username'],
                    $dbConfig['password'],
                    [
                        // STRICT_TRANS_TABLES,​ERROR_FOR_DIVISION_BY_ZERO,​NO_AUTO_CREATE_USER,​NO_ENGINE_SUBSTITUTION
                        \PDO::MYSQL_ATTR_INIT_COMMAND =>
                            "SET NAMES '{$dbConfig['charset']}' COLLATE '{$dbConfig['collation']}', @@session.sql_mode='" . implode(',', [
                                'STRICT_TRANS_TABLES',
                                'ERROR_FOR_DIVISION_BY_ZERO',
                                'NO_AUTO_CREATE_USER',
                                'NO_ENGINE_SUBSTITUTION',
                                'NO_ZERO_DATE,NO_ZERO_IN_DATE',
                            ]) . "'",
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    ]
                );
//            } catch (\Exception $e) {
//                die('asdsadasd');
//            }
//            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            // This is necessary to get native types in results (for example, INT columns as PHP integers)
            $db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            if ($this->db === null) {
                $this->db = $db;
            }
            return $db;
        }
        return $this->db;
    }

    /**
     * @return \League\Plates\Engine
     */
    public function getTemplateEngine(): \League\Plates\Engine
    {
        $templateEngine = new \League\Plates\Engine(__DIR__ . '/Web/views', 'phtml');
        $templateEngine->loadExtensions([
            new Web\Components\Plates\UrlFromRouteExtension($this->getWebRouter()),
            new Web\Components\Plates\DurationFormatterExtension(),
        ]);
        return $templateEngine;
    }
}
