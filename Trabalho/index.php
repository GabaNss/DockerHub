<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

render_page_start('Portal do aluno', 'index.php', false);
?>
<div class="bg-light p-4 mb-4 rounded border">
    <h1 class="h2 text-center mb-2">Servidor Web da disciplina de VTPDWE2 2026/01</h1>
    <p class="text-center text-muted mb-0">
        Página inicial do aluno com links para a busca de CEP e para o contador de acessos.
    </p>
</div>

<div class="page-box p-4">
    <div class="row g-4 align-items-center">
        <div class="col-md-6 portal-profile">
            <div class="text-center">
                <img
                    class="profile-photo mx-auto mb-3"
                    src="assets/images/foto.jpeg"
                    alt="Foto de perfil de Gabriel Nossa Theodoro"
                >
                <h2 class="h4">Gabriel Nossa Theodoro</h2>
                <p class="text-muted mb-3">Aluno da disciplina VTPDWE2 2026/01.</p>
            </div>
        </div>

        <div class="col-md-6">
            <h2 class="h4 mb-3">Links</h2>
            <div class="list-group mb-3">
                <a class="list-group-item list-group-item-action" href="busca-cep.php">Página de busca CEP</a>
                <a class="list-group-item list-group-item-action" href="logs.php">Página do contador de acessos</a>
            </div>
        </div>
    </div>
</div>
<script>
(() => {
    let sent = false;

    const sendHomeTrack = () => {
        if (sent || document.visibilityState !== 'visible') {
            return;
        }

        sent = true;

        const payload = new FormData();
        payload.append('page', 'index.php');

        if (navigator.sendBeacon) {
            navigator.sendBeacon('track.php', payload);
            return;
        }

        fetch('track.php', {
            method: 'POST',
            body: payload,
            credentials: 'same-origin',
            keepalive: true
        }).catch(() => {});
    };

    window.addEventListener('pageshow', sendHomeTrack, { once: true });
    document.addEventListener('visibilitychange', sendHomeTrack);
    sendHomeTrack();
})();
</script>
<?php render_page_end(); ?>
