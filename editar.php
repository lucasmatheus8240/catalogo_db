<?php
// ###############################################
// # ARQUIVO: editar.php - Atualização (UPDATE)  #
// ###############################################
require_once "conexao.php";

// 1. Recebe e valida o ID via GET
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$produto = null;
$erro = null;

if (!$id) {
    header("Location: index.php");
    exit;
}

// 2. Carrega o produto existente
try {
    $sql_prod = "SELECT * FROM produtos WHERE id = :id";
    $stmt_prod = $conexao->prepare($sql_prod);
    $stmt_prod->execute([':id' => $id]);
    $produto = $stmt_prod->fetch();

    if (!$produto) {
        $erro = "Produto não encontrado.";
    }

    // Busca categorias
    $consulta_cat = $conexao->query("SELECT id, nome FROM categorias ORDER BY nome");
    $categorias = $consulta_cat->fetchAll();

} catch (PDOException $e) {
    $erro = "Erro ao carregar dados: " . $e->getMessage();
}


// 3. PROCESSA O POST (Atualização)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ID oculto enviado pelo formulário
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // --- CORREÇÃO PRINCIPAL ---
    // Converte vírgula para ponto e força conversão
    $preco = str_replace(',', '.', $_POST['preco']);
    $preco = floatval($preco);

    $categoria_id = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);

    // Validação corrigida
    if (!$id || empty($nome) || $preco <= 0 || !$categoria_id) {
        $erro = "Falha na validação dos dados. Verifique os campos obrigatórios.";
    } else {
        try {
            // 4. Atualiza o registro
            $sql = "UPDATE produtos 
                    SET nome = :nome, descricao = :descricao, preco = :preco, categoria_id = :categoria_id
                    WHERE id = :id";

            $stmt = $conexao->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':descricao' => $descricao,
                ':preco' => $preco,
                ':categoria_id' => $categoria_id,
                ':id' => $id
            ]);

            // Redireciona após atualizar
            header("Location: index.php?status=editado");
            exit;

        } catch (PDOException $e) {
            $erro = "Erro ao atualizar: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>✍️ Editar Produto</h1>
        <nav>
            <a href="index.php" class="botao-novo">🏠 Voltar para a Listagem</a>
        </nav>
    </header>

    <main>
        <?php if ($erro): ?>
            <p class="mensagem-erro"><?= $erro ?></p>

        <?php elseif ($produto): ?>
            <form action="editar.php?id=<?= $produto->id ?>" method="POST" onsubmit="return validarFormularioProduto()">
                <fieldset>
                    <legend>Editar: <?= htmlspecialchars($produto->nome) ?></legend>
                    
                    <input type="hidden" name="id" value="<?= $produto->id ?>">

                    <div class="campo">
                        <label for="nome">Nome do Produto: *</label>
                        <input type="text" id="nome" name="nome" required maxlength="255" 
                               value="<?= htmlspecialchars($produto->nome) ?>">
                        <span id="erro-nome" class="erro-validacao"></span>
                    </div>

                    <div class="campo">
                        <label for="descricao">Descrição:</label>
                        <textarea id="descricao" name="descricao"><?= htmlspecialchars($produto->descricao) ?></textarea>
                    </div>

                    <div class="campo-grupo">
                        <div class="campo">
                            <label for="preco">Preço (R$): *</label>
                            <input type="text" id="preco" name="preco" required 
                                   value="<?= number_format($produto->preco, 2, ',', '.') ?>">
                            <span id="erro-preco" class="erro-validacao"></span>
                        </div>

                        <div class="campo">
                            <label for="categoria_id">Categoria: *</label>
                            <select id="categoria_id" name="categoria_id" required>
                                <option value="">-- Selecione --</option>

                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?= $categoria->id ?>"
                                        <?= ($produto->categoria_id == $categoria->id) ? 'selected' : '' ?>>
                                        <?= $categoria->nome ?>
                                    </option>
                                <?php endforeach; ?>

                            </select>
                            <span id="erro-categoria" class="erro-validacao"></span>
                        </div>
                    </div>

                    <button type="submit" class="botao-principal">Salvar Alterações</button>
                </fieldset>
            </form>
        <?php endif; ?>
    </main>

    <script src="script.js"></script>
</body>
</html>