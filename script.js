// ###############################################
// # ARQUIVO: script.js - Validação e Interação #
// ###############################################

// 1. Requisito 3a: Validação de Formulário antes do Envio
function validarFormularioProduto() {
    let valido = true;
    
    // 1.1. Resetar mensagens de erro anteriores (limpa os spans)
    document.querySelectorAll('.erro-validacao').forEach(el => el.textContent = '');
    
    // 1.2. Validação do Campo Nome
    const nomeInput = document.getElementById('nome');
    if (nomeInput && nomeInput.value.trim() === '') {
        document.getElementById('erro-nome').textContent = 'O nome do produto é obrigatório.';
        valido = false;
    }

    // 1.3. Validação do Campo Preço
    const precoInput = document.getElementById('preco');
    const preco = parseFloat(precoInput ? precoInput.value : 0);
    // Checa se não é um número (isNaN) ou se é menor ou igual a zero
    if (precoInput && (isNaN(preco) || preco <= 0)) {
        document.getElementById('erro-preco').textContent = 'O preço deve ser um valor numérico positivo.';
        valido = false;
    }

    // 1.4. Validação do Campo Categoria (Select)
    const categoriaSelect = document.getElementById('categoria_id');
    // Checa se o valor selecionado é a opção padrão vazia
    if (categoriaSelect && categoriaSelect.value === '') {
        document.getElementById('erro-categoria').textContent = 'Selecione uma categoria.';
        valido = false;
    }

    // Retorna true (se válido) para permitir o envio do formulário, ou false para bloqueá-lo.
    return valido;
}

// 2. Requisito 3b: Interação Visual (Confirmação para Exclusão)
function confirmarExclusao(event, nomeProduto) {
    // Exibe a caixa de diálogo nativa do navegador (modal simples)
    const confirmou = confirm(`Tem certeza que deseja EXCLUIR o produto "${nomeProduto}"? Esta ação é irreversível.`);
    
    if (!confirmou) {
        // Se o usuário clicar em "Cancelar", impede o comportamento padrão do link (o href)
        event.preventDefault(); 
        return false;
    }
    // Se o usuário clicar em "OK", o link é seguido e a página 'excluir.php' é carregada.
    return true;
}