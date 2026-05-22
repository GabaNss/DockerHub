<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/auth.php';

$errorMessage = '';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    logout_logs_user();
    header('Location: logs.php');
    exit;
}

if (!is_logs_authenticated() && $requestMethod === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $password = (string) ($_POST['password'] ?? '');

    if (login_logs_user($password)) {
        header('Location: logs.php');
        exit;
    }

    $errorMessage = 'Senha incorreta. Tente novamente.';
}

if (is_logs_authenticated() && $requestMethod === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'clear_page') {
        $page = (string) ($_POST['page'] ?? '');

        if (in_array($page, TRACKED_PAGES, true)) {
            clear_counter($page);
        }
    } elseif ($action === 'clear_all') {
        clear_all_counters();
    } elseif ($action === 'clear_logs') {
        clear_logs();
    }

    // Evita que o redirecionamento após uma limpeza registre um novo acesso automaticamente.
    $_SESSION[SESSION_KEY_SKIP_LOG_TRACK] = true;

    header('Location: logs.php');
    exit;
}

if (is_logs_authenticated()) {
    if (!empty($_SESSION[SESSION_KEY_SKIP_LOG_TRACK])) {
        unset($_SESSION[SESSION_KEY_SKIP_LOG_TRACK]);
    } else {
        track_page_access('logs.php');
    }

    $counters = read_counters();
    arsort($counters);
    $totalAccesses = array_sum($counters);
    $logs = read_logs();
} else {
    $counters = [];
    $totalAccesses = 0;
    $logs = [];
}

render_page_start('Logs e estatísticas', 'logs.php');
?>
<div class="bg-light p-4 mb-4 rounded border">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1 class="h2 mb-2">Contador de acessos e logs</h1>
            <p class="mb-0">Painel protegido por senha para visualizar estatísticas e limpar dados.</p>
        </div>
        <?php if (is_logs_authenticated()) : ?>
            <a class="btn btn-outline-secondary" href="logs.php?action=logout">Sair</a>
        <?php endif; ?>
    </div>
</div>

<?php if (!is_logs_authenticated()) : ?>
    <div class="login-box p-4">
        <h2 class="h4">Login</h2>
        <p class="text-muted">Use a senha cadastrada para acessar a área administrativa.</p>

        <?php if ($errorMessage !== '') : ?>
            <div class="alert alert-danger"><?php echo e($errorMessage); ?></div>
        <?php endif; ?>

        <form method="post" action="logs.php" class="row g-3">
            <input type="hidden" name="action" value="login">

            <div class="col-md-6">
                <label for="password" class="form-label">Senha</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="form-control"
                    required
                    placeholder="Digite a senha"
                >
            </div>

            <div class="col-12">
                <button class="btn btn-primary" type="submit">Entrar</button>
            </div>
        </form>
    </div>
<?php else : ?>
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="info-card p-4 h-100">
                <h2 class="h6 text-muted">Total de acessos</h2>
                <p class="display-6 mb-0"><?php echo $totalAccesses; ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card p-4 h-100">
                <h2 class="h6 text-muted">Páginas monitoradas</h2>
                <p class="display-6 mb-0"><?php echo count($counters); ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card p-4 h-100">
                <h2 class="h6 text-muted">Quantidade de logs</h2>
                <p class="display-6 mb-0"><?php echo count($logs); ?></p>
            </div>
        </div>
    </div>

    <div class="page-box p-4 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h2 class="h4 mb-1">Estatísticas por página</h2>
                <p class="text-muted mb-0">Ordem decrescente de acessos.</p>
            </div>

            <form
                method="post"
                action="logs.php"
                onsubmit="return confirm('Tem certeza que deseja limpar todos os acessos?');"
            >
                <input type="hidden" name="action" value="clear_all">
                <button class="btn btn-danger" type="submit">Limpar todos os acessos</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Página</th>
                        <th>Acessos</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($counters as $page => $count) : ?>
                        <tr>
                            <td><?php echo e(page_label($page)); ?></td>
                            <td><?php echo (int) $count; ?></td>
                            <td>
                                <form
                                    method="post"
                                    action="logs.php"
                                    onsubmit="return confirm('Limpar acessos desta página?');"
                                    class="d-inline"
                                >
                                    <input type="hidden" name="action" value="clear_page">
                                    <input type="hidden" name="page" value="<?php echo e($page); ?>">
                                    <button class="btn btn-outline-secondary btn-sm" type="submit">Limpar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="page-box p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h2 class="h4 mb-1">Logs detalhados</h2>
                <p class="text-muted mb-0">Lista dos acessos com página, data, IP e navegador.</p>
            </div>

            <form
                method="post"
                action="logs.php"
                onsubmit="return confirm('Tem certeza que deseja apagar todos os logs?');"
            >
                <input type="hidden" name="action" value="clear_logs">
                <button class="btn btn-danger" type="submit">Limpar logs</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Página</th>
                        <th>Data/Hora</th>
                        <th>IP</th>
                        <th>Navegador</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($logs === []) : ?>
                        <tr>
                            <td class="text-center text-muted" colspan="5">Nenhum log registrado no momento.</td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($logs as $index => $log) : ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo e(page_label($log['page'])); ?></td>
                                <td><?php echo e($log['datetime']); ?></td>
                                <td><?php echo e($log['ip']); ?></td>
                                <td><?php echo e($log['user_agent']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
<?php render_page_end(); ?>
