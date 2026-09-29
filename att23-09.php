<?php
session_start();

if (!isset($_SESSION['produtos'])) {
    $_SESSION['produtos'] = [
        [
            'nome' => 'Teclado mecânico',
            'categoria' => 'Periféricos',
            'preco' => 320.00,
            'quantidade' => 12,
            'quantidadeVendida' => 5,
        ],
        [
            'nome' => 'Monitor 24',
            'categoria' => 'Monitores',
            'preco' => 890.00,
            'quantidade' => 6,
            'quantidadeVendida' => 2,
        ],
        [
            'nome' => 'Mouse sem fio',
            'categoria' => 'Periféricos',
            'preco' => 120.00,
            'quantidade' => 18,
            'quantidadeVendida' => 9,
        ],
        [
            'nome' => 'Notebook Gamer',
            'categoria' => 'Informática',
            'preco' => 4200.00,
            'quantidade' => 4,
            'quantidadeVendida' => 1,
        ],
    ];
}

$mensagem = '';
$produtos = $_SESSION['produtos'];
$pesquisa = trim($_POST['pesquisa'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'cadastrar') {
        $nome = trim($_POST['nome'] ?? '');
        $categoria = trim($_POST['categoria'] ?? '');
        $preco = (float) ($_POST['preco'] ?? 0);
        $quantidade = (int) ($_POST['quantidade'] ?? 0);
        $quantidadeVendida = (int) ($_POST['quantidadeVendida'] ?? 0);

        if (
            $nome !== '' &&
            $categoria !== '' &&
            $preco > 0 &&
            $quantidade >= 0 &&
            $quantidadeVendida >= 0
        ) {
            $_SESSION['produtos'][] = [
                'nome' => $nome,
                'categoria' => $categoria,
                'preco' => $preco,
                'quantidade' => $quantidade,
                'quantidadeVendida' => $quantidadeVendida,
            ];
            $mensagem = 'Produto cadastrado com sucesso!';
        } else {
            $mensagem = 'Preencha todos os campos corretamente.';
        }
    }

    if ($acao === 'remover_primeiro') {
        if (!empty($_SESSION['produtos'])) {
            array_shift($_SESSION['produtos']);
            $mensagem = 'Primeiro produto removido.';
        }
    }

    if ($acao === 'remover_ultimo') {
        if (!empty($_SESSION['produtos'])) {
            array_pop($_SESSION['produtos']);
            $mensagem = 'Último produto removido.';
        }
    }

    if ($acao === 'remover_id') {
        $posicao = (int) ($_POST['indice'] ?? -1);
        if ($posicao >= 0 && isset($_SESSION['produtos'][$posicao])) {
            array_splice($_SESSION['produtos'], $posicao, 1);
            $mensagem = 'Produto removido.';
        }
    }

    if ($acao === 'pesquisar') {
        $pesquisa = trim($_POST['pesquisa'] ?? '');
    }

    $produtos = $_SESSION['produtos'];
}

if ($pesquisa !== '') {
    $produtosFiltrados = [];

    foreach ($produtos as $indice => $produto) {
        if (
            stripos($produto['nome'], $pesquisa) !== false ||
            stripos($produto['categoria'], $pesquisa) !== false
        ) {
            $produtosFiltrados[] = [
                'indice' => $indice,
                'produto' => $produto
            ];
        }
    }
} else {
    $produtosFiltrados = [];

    foreach ($produtos as $indice => $produto) {
        $produtosFiltrados[] = [
            'indice' => $indice,
            'produto' => $produto
        ];
    }
}

$valorTotalEstoque = 0;
$maiorEstoque = null;
$menorEstoque = null;
$maisCaro = null;
$maisBarato = null;
$maisVendido = null;
$menosVendido = null;

foreach ($produtos as $produto) {
    $valorTotalEstoque += $produto['preco'] * $produto['quantidade'];

    if ($maiorEstoque === null || $produto['quantidade'] > $maiorEstoque['quantidade']) {
        $maiorEstoque = $produto;
    }

    if ($menorEstoque === null || $produto['quantidade'] < $menorEstoque['quantidade']) {
        $menorEstoque = $produto;
    }

    if ($maisCaro === null || $produto['preco'] > $maisCaro['preco']) {
        $maisCaro = $produto;
    }

    if ($maisBarato === null || $produto['preco'] < $maisBarato['preco']) {
        $maisBarato = $produto;
    }

    if ($maisVendido === null || $produto['quantidadeVendida'] > $maisVendido['quantidadeVendida']) {
        $maisVendido = $produto;
    }

    if ($menosVendido === null || $produto['quantidadeVendida'] < $menosVendido['quantidadeVendida']) {
        $menosVendido = $produto;
    }
}

$produtoMaisEstoque = $maiorEstoque ?? ['nome' => 'N/A'];
$produtoMenosEstoque = $menorEstoque ?? ['nome' => 'N/A'];
$produtoMaisCaro = $maisCaro ?? ['nome' => 'N/A', 'preco' => 0];
$produtoMaisBarato = $maisBarato ?? ['nome' => 'N/A', 'preco' => 0];
$produtoMaisVendido = $maisVendido ?? ['nome' => 'N/A'];
$produtoMenosVendido = $menosVendido ?? ['nome' => 'N/A'];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Produtos</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="container">
        <h1>Controle de Produtos</h1>

        <?php if ($mensagem !== ''): ?>
            <div class="alert"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <div class="painel">
            <div class="card">
                <h2>Cadastrar produto</h2>
                <form method="POST">
                    <div class="form-grid">
                        <div class="campo">
                            <label>Nome do produto</label>
                            <input type="text" name="nome" required>
                        </div>
                        <div class="campo">
                            <label>Categoria</label>
                            <input type="text" name="categoria" required>
                        </div>
                        <div class="campo">
                            <label>Preço</label>
                            <input type="number" name="preco" step="0.01" min="0.01" required>
                        </div>
                        <div class="campo">
                            <label>Quantidade em estoque</label>
                            <input type="number" name="quantidade" min="0" required>
                        </div>
                        <div class="campo">
                            <label>Quantidade vendida</label>
                            <input type="number" name="quantidadeVendida" min="0" value="0">
                        </div>
                    </div>
                    <button type="submit" name="acao" value="cadastrar">Cadastrar Produto</button>
                </form>
            </div>

            <div class="card">
                <h2>Ações rápidas</h2>
                <form method="POST" class="acoes">
                    <button type="submit" name="acao" value="remover_primeiro">Excluir primeiro</button>
                    <button type="submit" name="acao" value="remover_ultimo">Excluir último</button>
                </form>

                <form method="POST" class="pesquisa-form">
                    <label for="pesquisa">Pesquisar</label>
                    <div class="search-box">
                        <input type="text" id="pesquisa" name="pesquisa" value="<?php echo htmlspecialchars($pesquisa); ?>" placeholder="Nome ou categoria">
                        <button type="submit" name="acao" value="pesquisar">Pesquisar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="resumo-grid">
            <div class="resumo-box">
                <span>Maior estoque</span>
                <strong><?php echo htmlspecialchars($produtoMaisEstoque['nome']); ?></strong>
            </div>
            <div class="resumo-box">
                <span>Menor estoque</span>
                <strong><?php echo htmlspecialchars($produtoMenosEstoque['nome']); ?></strong>
            </div>
            <div class="resumo-box">
                <span>Produto mais caro</span>
                <strong><?php echo htmlspecialchars($produtoMaisCaro['nome']); ?></strong>
            </div>
            <div class="resumo-box">
                <span>Produto mais barato</span>
                <strong><?php echo htmlspecialchars($produtoMaisBarato['nome']); ?></strong>
            </div>
            <div class="resumo-box">
                <span>Mais vendido</span>
                <strong><?php echo htmlspecialchars($produtoMaisVendido['nome']); ?></strong>
            </div>
            <div class="resumo-box">
                <span>Menos vendido</span>
                <strong><?php echo htmlspecialchars($produtoMenosVendido['nome']); ?></strong>
            </div>
            <div class="resumo-box destaque">
                <span>Valor total em estoque</span>
                <strong>R$ <?php echo number_format($valorTotalEstoque, 2, ',', '.'); ?></strong>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th>Quantidade</th>
                        <th>Vendidas</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($produtosFiltrados)): ?>
                        <tr>
                            <td colspan="6" class="empty">Nenhum produto encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($produtosFiltrados as $item): ?>

                            <?php
                            $indice = $item['indice'];
                            $produto = $item['produto'];
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                                <td><?php echo htmlspecialchars($produto['categoria']); ?></td>
                                <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                                <td><?php echo (int) $produto['quantidade']; ?></td>
                                <td><?php echo (int) $produto['quantidadeVendida']; ?></td>
                                <td>
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="indice" value="<?php echo $indice; ?>">
                                        <button type="submit" name="acao" value="remover_id">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>