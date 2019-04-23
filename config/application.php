<?php

return [
    'mysql-server-name' => getenv('MYSQL_SERVER_NAME'),
    'hide-current-process' => (bool) getenv('HIDE_CURRENT_PROCESS', true),
];
