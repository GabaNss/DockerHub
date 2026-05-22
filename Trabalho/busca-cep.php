<?php
declare(strict_types=1);

use Claudsonm\CepPromise\CepPromise;
use Claudsonm\CepPromise\Exceptions\CepPromiseException;
use Claudsonm\CepPromise\Exceptions\CepPromiseProviderException;
use Claudsonm\CepPromise\Providers\CepAbertoProvider;
use Claudsonm\CepPromise\Providers\ViaCepProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/layout.php';

error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

track_page_access('busca-cep.php');
$accessCount = page_access_count('busca-cep.php');

final class LocalViaCepProvider extends ViaCepProvider
{
    public function __construct(?Client $client = null)
    {
        parent::__construct($client ?? new Client(['verify' => false]));
    }
}

final class LocalCepAbertoProvider extends CepAbertoProvider
{
    public function __construct(?Client $client = null)
    {
        parent::__construct($client ?? new Client(['verify' => false]));
    }

    public function makePromise(string $cep)
    {
        $url = "https://www.cepaberto.com/api/v3/cep?cep=$cep";
        $options = [
            'headers' => [
                'Authorization' => 'Token token=' . self::CEP_ABERTO_TOKEN,
                'Content-Type' => 'application/json;charset=utf-8',
            ],
        ];

        $this->promise = $this->client
            ->requestAsync('GET', $url, $options)
            ->then(function (ResponseInterface $response) {
                return json_decode($response->getBody()->getContents(), true) ?? [];
            })
            ->then(function (array $responseArray) use ($cep) {
                if ($responseArray === []) {
                    throw new Exception('CEP nao encontrado na base do CEP Aberto.');
                }

                return [
                    'zipCode' => (string) ($responseArray['cep'] ?? $cep),
                    'state' => (string) ($responseArray['estado']['sigla'] ?? $responseArray['estado'] ?? ''),
                    'city' => (string) ($responseArray['cidade']['nome'] ?? $responseArray['cidade'] ?? ''),
                    'district' => (string) ($responseArray['bairro'] ?? ''),
                    'street' => (string) ($responseArray['logradouro'] ?? ''),
                    'provider' => $this->providerIdentifier,
                ];
            })
            ->otherwise(function (Throwable $exception) {
                if ($exception instanceof RequestException) {
                    $message = 'Erro ao se conectar com o servico CEP Aberto.';
                }

                throw new CepPromiseProviderException(
                    $message ?? $exception->getMessage(),
                    $this->providerIdentifier
                );
            });

        return $this->promise;
    }
}

function formatCepError(mixed $error): string
{
    if (is_array($error) && isset($error['message'])) {
        return (string) $error['message'];
    }

    if ($error instanceof Throwable) {
        return $error->getMessage();
    }

    if (is_string($error) && $error !== '') {
        return $error;
    }

    return 'Nao foi possivel consultar o CEP no momento.';
}

function formatAddressValue(mixed $value): string
{
    if (is_string($value)) {
        $value = trim($value);

        return $value !== '' ? $value : 'Nao informado';
    }

    if (is_scalar($value)) {
        $value = trim((string) $value);

        return $value !== '' ? $value : 'Nao informado';
    }

    if (is_array($value)) {
        $preferred = $value['nome'] ?? $value['sigla'] ?? null;

        if (is_scalar($preferred)) {
            $preferred = trim((string) $preferred);

            return $preferred !== '' ? $preferred : 'Nao informado';
        }
    }

    return 'Nao informado';
}

$resultado = null;
$erros = [];
$cepFormatado = null;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && !empty($_POST['cep'])) {
    $cepRaw = preg_replace('/\D/', '', (string) $_POST['cep']);

    if (strlen($cepRaw) === 8) {
        $cepFormatado = substr($cepRaw, 0, 5) . '-' . substr($cepRaw, 5, 3);
    } else {
        $cepFormatado = $cepRaw;
    }

    try {
        $resultado = CepPromise::fetch($cepRaw, [
            LocalViaCepProvider::class,
            LocalCepAbertoProvider::class,
        ]);
    } catch (CepPromiseException $e) {
        $erros = array_map('formatCepError', $e->toArray()['errors'] ?? []);

        if ($erros === []) {
            $erros[] = $e->getMessage();
        }
    } catch (Throwable $e) {
        $erros[] = formatCepError($e);
    }
}

render_page_start('Busca CEP', 'busca-cep.php', false);
?>
<div class="bg-light p-4 mb-4 rounded border">
    <h1 class="h2">Composer - Busca CEP com Composer</h1>
</div>

<div class="page-box p-4">
    <form action="busca-cep.php" method="POST" class="row g-3">
        <div class="col-md-4">
            <label for="cep" class="form-label">CEP</label>
            <input
                class="form-control"
                type="text"
                name="cep"
                id="cep"
                required
                autofocus
                maxlength="9"
                placeholder="Somente numeros"
                value="<?php echo e((string) ($_POST['cep'] ?? '')); ?>"
            >
        </div>
        <div class="col-md-4 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary">Enviar</button>
            <button type="reset" class="btn btn-warning">Limpar</button>
        </div>
    </form>

    <?php if ($resultado !== null) : ?>
        <div class="card mt-4 border-success">
            <div class="card-body bg-success-subtle">
                <h2 class="h5 fw-bold">CEP: <?php echo e((string) $cepFormatado); ?></h2>
                <p class="mb-1">Rua: <?php echo e(formatAddressValue($resultado->street ?? null)); ?></p>
                <p class="mb-1">Bairro: <?php echo e(formatAddressValue($resultado->district ?? null)); ?></p>
                <p class="mb-1">Cidade: <?php echo e(formatAddressValue($resultado->city ?? null)); ?></p>
                <p class="mb-0">Estado: <?php echo e(formatAddressValue($resultado->state ?? null)); ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($erros !== []) : ?>
        <div class="card mt-4 border-danger">
            <div class="card-body bg-danger-subtle">
                <h2 class="h5 fw-bold">CEP: <?php echo e((string) $cepFormatado); ?></h2>
                <p class="mb-2">Detalhes do erro</p>
                <ul class="mb-0">
                    <?php foreach ($erros as $mensagemErro) : ?>
                        <li><?php echo e($mensagemErro); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php render_page_end(); ?>
