<?php
declare(strict_types=1);

define('APP_NAME', 'Cronicas de Hyrule');
define('APP_TIMEZONE', 'America/Sao_Paulo');
define('LOGS_PASSWORD', 'senha_da_nasa');

define('SESSION_KEY_LOGGED_IN', 'logs_authenticated');
define('SESSION_KEY_SKIP_LOG_TRACK', 'skip_next_logs_page_track');

define('PROJECT_ROOT', dirname(__DIR__));
define('DATA_DIR', PROJECT_ROOT . '/data');
define('COUNTERS_FILE', DATA_DIR . '/counters.json');
define('LOG_FILE', DATA_DIR . '/access_logs.txt');
define('LEGACY_LOG_FILE', DATA_DIR . '/access_logs.jsonl');

define('TRACKED_PAGES', [
    'index.php',
    'busca-cep.php',
    'inicio.php',
    'sobre.php',
    'contato.php',
    'logs.php',
]);

date_default_timezone_set(APP_TIMEZONE);
