<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

track_page_access('sobre.php');

render_page_start("Majora's Mask", 'sobre.php');
?>
<div class="bg-light p-4 mb-4 rounded border">
    <h1 class="h2">The Legend of Zelda: Majora's Mask</h1>
    <p class="mb-0">
        Lancado em 2000, Majora's Mask pegou a base de Ocarina of Time e criou uma experiencia muito mais estranha,
        densa e emocional, marcada pelo fim do mundo e pelo ciclo de tres dias.
    </p>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="page-box p-4 page-text h-100">
            <p>
                Mesmo reutilizando recursos de Ocarina of Time, Majora's Mask conseguiu construir uma personalidade propria.
                A rotina dos habitantes de Clock Town faz o mundo parecer vivo, e cada repeticao do tempo revela novos
                detalhes sobre seus problemas e sonhos.
            </p>
            <p class="mb-0">
                Por isso, ele e visto como um dos capitulos mais criativos da franquia: um jogo menor em escala tecnica,
                mas enorme em atmosfera, simbolismo e impacto emocional.
            </p>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="page-box p-2 h-100">
            <img
                class="game-photo game-photo-lg"
                src="assets/images/MM.jpg"
                alt="Capa de The Legend of Zelda: Majora's Mask"
            >
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="info-card h-100 p-4">
            <h2 class="h5">Relogio de tres dias</h2>
            <p class="mb-0">
                O grande diferencial esta no tempo limitado. Cada ciclo exige planejamento, observacao e escolhas
                cuidadosas para salvar Termina antes que a lua caia.
            </p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-card h-100 p-4">
            <h2 class="h5">Mascaras com funcao real</h2>
            <p class="mb-0">
                As mascaras nao sao apenas colecionaveis. Elas alteram habilidades, abrem historias paralelas e
                tornam Link capaz de assumir identidades bem diferentes.
            </p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-card h-100 p-4">
            <h2 class="h5">Tom mais sombrio</h2>
            <p class="mb-0">
                O jogo fala sobre perda, medo e despedida de forma delicada. Isso faz com que Majora's Mask seja
                lembrado como o Zelda mais melancolico e inquietante.
            </p>
        </div>
    </div>
</div>
<?php render_page_end(); ?>
