<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function ensure_storage_files(): void
{
    if (!is_dir(DATA_DIR)) {
        mkdir(DATA_DIR, 0775, true);
    }

    if (!is_file(COUNTERS_FILE)) {
        $payload = json_encode(get_default_counters(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents(COUNTERS_FILE, ($payload ?: '{}') . PHP_EOL, LOCK_EX);
    }

    if (!is_file(LOG_FILE) && is_file(LEGACY_LOG_FILE)) {
        if (!@rename(LEGACY_LOG_FILE, LOG_FILE)) {
            $legacyContents = file_get_contents(LEGACY_LOG_FILE);

            if ($legacyContents !== false) {
                file_put_contents(LOG_FILE, $legacyContents, LOCK_EX);
                @unlink(LEGACY_LOG_FILE);
            }
        }
    }

    if (!is_file(LOG_FILE)) {
        touch(LOG_FILE);
    }
}

function get_default_counters(): array
{
    $defaults = [];

    foreach (TRACKED_PAGES as $page) {
        $defaults[$page] = 0;
    }

    return $defaults;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function with_locked_file(string $filePath, callable $callback)
{
    $handle = fopen($filePath, 'c+');

    if ($handle === false) {
        throw new RuntimeException(sprintf('Não foi possível abrir o arquivo %s.', $filePath));
    }

    try {
        if (!flock($handle, LOCK_EX)) {
            throw new RuntimeException(sprintf('Não foi possível bloquear o arquivo %s.', $filePath));
        }

        try {
            return $callback($handle);
        } finally {
            fflush($handle);
            flock($handle, LOCK_UN);
        }
    } finally {
        fclose($handle);
    }
}

function normalize_counters(array $counters): array
{
    $normalized = get_default_counters();

    foreach ($counters as $page => $count) {
        if (!is_string($page)) {
            continue;
        }

        $normalized[$page] = max(0, (int) $count);
    }

    return $normalized;
}

function read_counters_from_handle($handle): array
{
    rewind($handle);
    $rawContents = stream_get_contents($handle);

    if ($rawContents === false || trim($rawContents) === '') {
        return get_default_counters();
    }

    $decoded = json_decode($rawContents, true);

    if (!is_array($decoded)) {
        return get_default_counters();
    }

    return normalize_counters($decoded);
}

function write_counters_to_handle($handle, array $counters): void
{
    $payload = json_encode(
        normalize_counters($counters),
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
    );

    if ($payload === false) {
        throw new RuntimeException('Não foi possível converter os contadores para JSON.');
    }

    rewind($handle);
    ftruncate($handle, 0);
    fwrite($handle, $payload . PHP_EOL);
}

function read_counters(): array
{
    ensure_storage_files();

    return with_locked_file(COUNTERS_FILE, static function ($handle): array {
        return read_counters_from_handle($handle);
    });
}

function increment_counter(string $page): int
{
    ensure_storage_files();

    return with_locked_file(COUNTERS_FILE, static function ($handle) use ($page): int {
        // O contador é lido e regravado com lock exclusivo para evitar perda de acessos simultâneos.
        $counters = read_counters_from_handle($handle);
        $counters[$page] = ($counters[$page] ?? 0) + 1;
        write_counters_to_handle($handle, $counters);

        return (int) $counters[$page];
    });
}

function register_log(string $page): void
{
    ensure_storage_files();

    $logEntry = [
        'page' => $page,
        'datetime' => date('Y-m-d H:i:s'),
        'ip' => get_client_ip(),
        'user_agent' => get_user_agent(),
    ];

    with_locked_file(LOG_FILE, static function ($handle) use ($logEntry): void {
        $payload = json_encode($logEntry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($payload === false) {
            throw new RuntimeException('Não foi possível registrar o log de acesso.');
        }

        fseek($handle, 0, SEEK_END);
        fwrite($handle, $payload . PHP_EOL);
    });
}

function read_logs(): array
{
    ensure_storage_files();

    return with_locked_file(LOG_FILE, static function ($handle): array {
        rewind($handle);
        $rawContents = stream_get_contents($handle);

        if ($rawContents === false || trim($rawContents) === '') {
            return [];
        }

        $lines = preg_split('/\R/', trim($rawContents)) ?: [];
        $logs = [];

        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $decoded = json_decode($line, true);

            if (!is_array($decoded)) {
                continue;
            }

            $logs[] = [
                'page' => (string) ($decoded['page'] ?? 'desconhecida'),
                'datetime' => (string) ($decoded['datetime'] ?? ''),
                'ip' => (string) ($decoded['ip'] ?? 'IP não identificado'),
                'user_agent' => (string) ($decoded['user_agent'] ?? 'Navegador não identificado'),
            ];
        }

        return $logs;
    });
}

function clear_counter(string $page): void
{
    ensure_storage_files();

    with_locked_file(COUNTERS_FILE, static function ($handle) use ($page): void {
        $counters = read_counters_from_handle($handle);
        $counters[$page] = 0;
        write_counters_to_handle($handle, $counters);
    });
}

function clear_all_counters(): void
{
    ensure_storage_files();

    with_locked_file(COUNTERS_FILE, static function ($handle): void {
        $counters = read_counters_from_handle($handle);

        foreach ($counters as $page => $count) {
            $counters[$page] = 0;
        }

        write_counters_to_handle($handle, $counters);
    });
}

function clear_logs(): void
{
    ensure_storage_files();

    with_locked_file(LOG_FILE, static function ($handle): void {
        rewind($handle);
        ftruncate($handle, 0);
    });
}

function track_page_access(string $page): void
{
    increment_counter($page);
    register_log($page);
}

function page_access_count(string $page): int
{
    $counters = read_counters();

    return (int) ($counters[$page] ?? 0);
}

function page_label(string $page): string
{
    $labels = [
        'index.php' => 'Home',
        'busca-cep.php' => 'Busca CEP',
        'inicio.php' => 'Ocarina of Time',
        'sobre.php' => "Majora's Mask",
        'contato.php' => 'Breath of the Wild',
        'logs.php' => 'Logs',
    ];

    return $labels[$page] ?? $page;
}

function get_client_ip(): string
{
    $forwardedFor = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';

    if (is_string($forwardedFor) && $forwardedFor !== '') {
        $candidates = array_map('trim', explode(',', $forwardedFor));

        foreach ($candidates as $candidate) {
            if (filter_var($candidate, FILTER_VALIDATE_IP)) {
                return $candidate;
            }
        }
    }

    $remoteAddress = $_SERVER['REMOTE_ADDR'] ?? 'IP não identificado';

    return is_string($remoteAddress) && $remoteAddress !== '' ? $remoteAddress : 'IP não identificado';
}

function get_user_agent(): string
{
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Navegador não identificado';

    return is_string($userAgent) && $userAgent !== '' ? $userAgent : 'Navegador não identificado';
}
