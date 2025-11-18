<?php
// #################################################################
// # ARQUIVO: index.php - Listagem (READ) e Painel Administrativo #
// #################################################################
require_once "conexao.php"; // Inclui o arquivo de conexão PDO

// 1. Lógica de Busca de Dados (READ)
try {
    // Consulta SQL: Busca dados do produto (p) e junta com o nome da categoria (c)
    // A cláusula JOIN relaciona a tabela 'produtos' com 'categorias' pelo campo 'categoria_id'.
    $sql = "SELECT p.id, p.nome, p.descricao, p.preco, c.nome AS categoria 
            FROM produtos p 
            JOIN categorias c ON p.categoria_id = c.id 
            ORDER BY p.id DESC";
            
    $consulta = $conexao->query($sql); // Executa a consulta SQL (para SELECT simples)
    $produtos = $consulta->fetchAll(); // Pega todos os resultados e armazena em $produtos
    
} catch (PDOException $e) {
    // Armazena a mensagem de erro se a consulta falhar
    $erro = "Erro ao buscar produtos: " . $e->getMessage();
}

// 2. Lógica de Mensagens Dinâmicas (Feedback do CRUD)
// Verifica se há um parâmetro 'status' na URL (após um cadastro, edição ou exclusão)
$mensagem = "";
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'cadastrado') {
        $mensagem = "✅ Produto cadastrado com sucesso!";
    } elseif ($_GET['status'] == 'editado') {
        $mensagem = "📝 Produto editado com sucesso!";
    } elseif ($_GET['status'] == 'excluido') {
        $mensagem = "❌ Produto excluído com sucesso!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Produtos - Admin</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>
    <header> <h1>🖥️ Painel Administrativo do Catálogo</h1>
        <nav>
            <a href="cadastrar.php" class="botao-novo">➕ Cadastrar Novo Produto</a>
        </nav>
    </header>

    <main> <h2>Lista de Produtos</h2>

        <?php if (!empty($mensagem)): ?>
            <p class="mensagem-sucesso"><?= $mensagem ?></p>
        <?php endif; ?>

        <?php if (isset($erro)): ?>
            <p class="mensagem-erro"><?= $erro ?></p>
        <?php endif; ?>

        <?php if (count($produtos) > 0): ?>
            <div class="tabela-responsiva">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Preço</th>
                            <th>Categoria</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                        <tr>
                            <td><?= $produto->id ?></td>
                            <td><?= $produto->nome ?></td>
                            <td>R$ <?= number_format($produto->preco, 2, ',', '.') ?></td>
                            <td><?= $produto->categoria ?></td>
                            <td>
                                <a href="editar.php?id=<?= $produto->id ?>" class="botao-acao editar">Editar</a>
                                <a href="excluir.php?id=<?= $produto->id ?>" class="botao-acao excluir" 
                                   onclick="return confirmarExclusao(event, '<?= $produto->nome ?>')">Excluir</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="sem-registros">Nenhum produto cadastrado ainda.</p>
        <?php endif; ?>
    </main>

    <footer> <p>&copy; 2025 Catálogo CRUD.</p>
    </footer>

    <script src="script.js"></script> </body>
</html>
