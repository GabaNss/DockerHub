<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/layout.php';

track_page_access('contato.php');

render_page_start('Breath of the Wild', 'contato.php');
?>
<div class="bg-light p-4 mb-4 rounded border">
    <h1 class="h2">The Legend of Zelda: Breath of the Wild</h1>
    <p class="mb-0">
        Lancado em 2017, Breath of the Wild reinventou Zelda ao apostar em exploracao livre, sistemas interligados
        e um mundo aberto que recompensa a curiosidade em cada direcao.
    </p>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="page-box p-4 page-text h-100">
            <p>
                Outro ponto forte de Breath of the Wild e a forma como ele conta sua historia. Em vez de prender o jogador
                a uma sequencia rigida, o jogo espalha memorias e pistas pelo mapa, deixando que cada pessoa reconstrua
                o passado de Link no proprio ritmo.
            </p>
            <p class="mb-0">
                O resultado e uma aventura que parece moderna sem perder o espirito da franquia, abrindo caminho para
                uma nova fase de Zelda nos consoles atuais.
            </p>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="page-box p-2 h-100">
            <img
                class="game-photo game-photo-lg"
                src="assets/images/BW.jpg"
                alt="Capa de The Legend of Zelda: Breath of the Wild"
            >
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="info-card h-100 p-4">
            <h2 class="h5">Liberdade real</h2>
            <p class="mb-0">
                Desde o inicio, o jogo deixa o jogador escolher caminhos, desafios e ordem de exploracao, o que cria
                uma sensacao constante de descoberta e aventura pessoal.
            </p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-card h-100 p-4">
            <h2 class="h5">Fisica e criatividade</h2>
            <p class="mb-0">
                Vento, fogo, eletricidade e gravidade interagem de forma natural. Isso permite resolver problemas
                de varias maneiras, quase sempre com espaco para improviso.
            </p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-card h-100 p-4">
            <h2 class="h5">Novo rumo para Zelda</h2>
            <p class="mb-0">
                O sucesso do jogo mostrou que a serie podia abandonar estruturas antigas e ainda assim manter a
                essencia de exploracao, misterio e superacao.
            </p>
        </div>
    </div>
</div>
<?php render_page_end(); ?>
