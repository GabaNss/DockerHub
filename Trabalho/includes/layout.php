<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

function render_page_start(
    string $title,
    string $activePage = '',
    bool $showNavigation = true,
    string $bodyClass = ''
): void {
    $menuItems = [
        'inicio.php' => 'Ocarina of Time',
        'sobre.php' => "Majora's Mask",
        'contato.php' => 'Breath of the Wild',
        'logs.php' => 'Logs',
    ];

    $bodyClassAttribute = $bodyClass !== '' ? ' class="' . e($bodyClass) . '"' : '';
    ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title); ?> | <?php echo e(APP_NAME); ?></title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body<?php echo $bodyClassAttribute; ?>>
    <div class="container py-3">
<?php if ($showNavigation) : ?>
        <header class="d-flex flex-wrap justify-content-between align-items-center py-3 mb-4 border-bottom gap-3">
            <a href="index.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-dark text-decoration-none">
                <span class="fs-4 fw-semibold"><?php echo e(APP_NAME); ?></span>
            </a>

            <div class="d-flex flex-wrap align-items-center gap-2">
                <a
                    class="home-link<?php echo $activePage === 'index.php' ? ' active' : ''; ?>"
                    href="index.php"
                    <?php echo $activePage === 'index.php' ? 'aria-current="page"' : ''; ?>
                >
                    Home
                </a>

                <ul class="nav nav-pills">
                    <?php foreach ($menuItems as $file => $label) : ?>
                        <li class="nav-item">
                            <a
                                class="nav-link<?php echo $file === $activePage ? ' active' : ''; ?>"
                                href="<?php echo e($file); ?>"
                                <?php echo $file === $activePage ? 'aria-current="page"' : ''; ?>
                            >
                                <?php echo e($label); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </header>
<?php endif; ?>
        <main>
<?php
}

function render_page_end(): void
{
    ?>
        </main>
        <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 mt-4 border-top small text-muted">
            <div>Projeto em PHP sem framework com persistencia em arquivos.</div>
            <div>Sabado Letivo - Web 2</div>
        </footer>
    </div>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"
    ></script>
</body>
</html>
<?php
}
