<?php
// #######################################################
// # ARQUIVO: conexao.php - Configuração da Conexão MySQL #
// #######################################################
// Este arquivo é responsável por estabelecer a conexão com o banco de dados
// usando a biblioteca PDO (PHP Data Objects), que é o método recomendado 
// e mais seguro para interagir com o MySQL em PHP.

// 1. Configurações do Banco de Dados
$servidor = "localhost"; // Onde o MySQL está rodando (geralmente 'localhost' no XAMPP)
$usuario = "root";       // Usuário padrão do MySQL no XAMPP
$senha = "";             // Senha padrão (geralmente vazia no XAMPP)
$banco = "catalogo_db";  // Nome do banco de dados criado

// 2. Tentativa de Conexão usando PDO
try {
    // Cria uma nova instância da classe PDO
    $conexao = new PDO(
        // String de conexão: driver (mysql), host e nome do banco de dados (dbname)
        "mysql:host=$servidor;dbname=$banco;charset=utf8", 
        $usuario, 
        $senha
    );
    
    // Define o modo de erro do PDO: Lançar exceções em caso de erros SQL.
    // Isso é crucial para debugar e tratar erros de forma robusta.
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Define o fetch mode padrão: Retorna resultados como objetos (mais fácil de acessar).
    $conexao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    // Se a conexão for bem-sucedida, o objeto $conexao estará pronto para uso.

} catch(PDOException $e) {
    // 3. Tratamento de Erro na Conexão
    // Se a conexão falhar (ex: servidor desligado, nome de BD errado), 
    // o script é interrompido com a mensagem de erro.
    die("Erro na conexão: " . $e->getMessage());
}
?>