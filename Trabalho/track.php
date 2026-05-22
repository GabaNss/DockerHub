<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    exit;
}

$page = (string) ($_POST['page'] ?? '');

if (!in_array($page, TRACKED_PAGES, true)) {
    http_response_code(400);
    exit;
}

track_page_access($page);

http_response_code(204);
