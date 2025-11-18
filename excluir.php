<?php
// ###############################################
// # ARQUIVO: excluir.php - Exclusão (DELETE) #
// ###############################################
require_once "conexao.php";

// 1. Recebe e valida o ID passado via URL (GET)
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    // Se o ID for inválido ou estiver faltando, redireciona
    header("Location: index.php");
    exit;
}

try {
    // 2. Prepara a Query SQL de Exclusão (DELETE)
    $sql = "DELETE FROM produtos WHERE id = :id"; 
    $stmt = $conexao->prepare($sql);
    
    // 3. Executa a Query
    $stmt->execute([':id' => $id]);

    // 4. Redireciona para a listagem com mensagem de sucesso
    header("Location: index.php?status=excluido");
    exit;

} catch (PDOException $e) {
    // 5. Tratamento de Erro (Ex: Tentativa de excluir um registro que possui chave estrangeira em outra tabela)
    header("Location: index.php?status=erro_exclusao&msg=" . urlencode("Erro ao excluir. Verifique a integridade dos dados."));
    exit;
}
?>
