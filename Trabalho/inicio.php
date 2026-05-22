<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

track_page_access('inicio.php');

render_page_start('Ocarina of Time', 'inicio.php');
?>
<div class="bg-light p-4 mb-4 rounded border">
    <h1 class="h2">The Legend of Zelda: Ocarina of Time</h1>
    <p class="mb-0">
        Lancado em 1998 para Nintendo 64, Ocarina of Time virou referencia por transformar a formula classica
        de Zelda em uma aventura 3D grandiosa, elegante e cheia de momentos marcantes.
    </p>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="page-box p-4 page-text h-100">
            <p>
                Ocarina of Time tambem ficou famoso por transformar a ocarina em parte do gameplay. As melodias nao
                servem apenas como detalhe estetico: elas abrem caminhos, mudam o clima e ajudam a construir a identidade
                de cada regiao de Hyrule.
            </p>
            <p class="mb-0">
                Para muita gente, este foi o jogo que consolidou a imagem moderna de Link, Zelda e Ganondorf, criando uma
                base que influenciou praticamente todas as aventuras 3D que vieram depois.
            </p>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="page-box p-2 h-100">
            <img
                class="game-photo game-photo-lg"
                src="assets/images/OT.jpg"
                alt="Capa de The Legend of Zelda: Ocarina of Time"
            >
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="info-card h-100 p-4">
            <h2 class="h5">Revolucao em 3D</h2>
            <p class="mb-0">
                O jogo apresentou combate com mira em alvos, exploracao em profundidade e dungeons desenhadas
                para aproveitar o espaco tridimensional sem perder o ritmo da serie.
            </p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-card h-100 p-4">
            <h2 class="h5">Viagem no tempo</h2>
            <p class="mb-0">
                Alternar entre Link crianca e adulto muda Hyrule por completo e reforca a sensacao de crescimento,
                responsabilidade e destino que move toda a jornada.
            </p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-card h-100 p-4">
            <h2 class="h5">Legado duradouro</h2>
            <p class="mb-0">
                Ate hoje ele e lembrado como um dos jogos mais influentes de todos os tempos por unir trilha sonora,
                progressao, narrativa simples e design de fases quase impecavel.
            </p>
        </div>
    </div>
</div>
<?php render_page_end(); ?>
