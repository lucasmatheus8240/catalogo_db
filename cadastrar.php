<?php
// ##################################################
// # ARQUIVO: cadastrar.php - Cadastro (CREATE) #
// ##################################################
require_once "conexao.php"; 

$nome = $descricao = $preco = $categoria_id = null;
$erro_form = $erro_cat = null;

// 1. Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 2. Recebe e Sanitiza/Filtra os dados (Prevenção de XSS e segurança)
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    // Valida se o preço é um float
    $preco = filter_input(INPUT_POST, 'preco', FILTER_VALIDATE_FLOAT); 
    $categoria_id = filter_input(INPUT_POST, 'categoria_id', FILTER_VALIDATE_INT);
    
    // 3. Validação do Servidor (Segurança adicional)
    if (!$nome || !$preco || $preco <= 0 || !$categoria_id) {
        $erro_form = "Por favor, preencha todos os campos obrigatórios corretamente.";
    } else {
        try {
            // 4. Prepara a Query SQL de Inserção (INSERT)
            // Usamos Prepared Statements (os placeholders :nome, :preco, etc.) para:
            // a) Prevenir SQL Injection (a principal vulnerabilidade de segurança).
            // b) Melhorar a performance na execução.
            $sql = "INSERT INTO produtos (nome, descricao, preco, categoria_id) 
                    VALUES (:nome, :descricao, :preco, :categoria_id)";
            $stmt = $conexao->prepare($sql);
            
            // 5. Executa a Query, passando um array com os valores para os placeholders
            $stmt->execute([
                ':nome' => $nome,
                ':descricao' => $descricao,
                ':preco' => $preco,
                ':categoria_id' => $categoria_id
            ]);

            // 6. Redireciona o usuário para a página de listagem com status de sucesso
            header("Location: index.php?status=cadastrado");
            exit;

        } catch (PDOException $e) {
            $erro_form = "Erro ao cadastrar: " . $e->getMessage();
        }
    }
}

// 7. Busca as Categorias para o campo <select>
try {
    $consulta_cat = $conexao->query("SELECT id, nome FROM categorias ORDER BY nome");
    $categorias = $consulta_cat->fetchAll();
} catch (PDOException $e) {
    $erro_cat = "Erro ao carregar categorias: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>✏️ Cadastrar Novo Produto</h1>
        <nav>
            <a href="index.php" class="botao-novo">🏠 Voltar para a Listagem</a>
        </nav>
    </header>

    <main>
        <?php if (isset($erro_form)): ?>
            <p class="mensagem-erro"><?= $erro_form ?></p>
        <?php endif; ?>

        <form action="cadastrar.php" method="POST" onsubmit="return validarFormularioProduto()">
            <fieldset>
                <legend>Dados do Produto</legend>
                
                <div class="campo">
                    <label for="nome">Nome do Produto: <span class="requerido">*</span></label>
                    <input type="text" id="nome" name="nome" required maxlength="255" value="<?= htmlspecialchars($nome ?? '') ?>">
                    <span id="erro-nome" class="erro-validacao"></span>
                </div>

                <div class="campo-grupo">
                    <div class="campo">
                        <label for="preco">Preço (R$): <span class="requerido">*</span></label>
                        <input type="number" id="preco" name="preco" required min="0.01" step="0.01" value="<?= htmlspecialchars($preco ?? '') ?>">
                        <span id="erro-preco" class="erro-validacao"></span>
                    </div>

                    <div class="campo">
                        <label for="categoria_id">Categoria: <span class="requerido">*</span></label>
                        <select id="categoria_id" name="categoria_id" required>
                            <option value="">-- Selecione uma Categoria --</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria->id ?>" <?= (($categoria_id ?? '') == $categoria->id) ? 'selected' : '' ?>>
                                    <?= $categoria->nome ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span id="erro-categoria" class="erro-validacao"></span>
                    </div>
                </div>

                <button type="submit" class="botao-principal">Salvar Produto</button>
            </fieldset>
        </form>
    </main>
    <script src="script.js"></script>
</body>
</html>