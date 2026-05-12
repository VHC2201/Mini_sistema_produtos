function atualizarBotaoCarrinho() {
  const checkboxes    = document.querySelectorAll('.chk-produto:checked');
  const btnAdicionar  = document.getElementById('btn-adicionar-carrinho');
  const contador      = document.getElementById('contador-selecionados');

  if (btnAdicionar) {
    btnAdicionar.disabled = checkboxes.length === 0;
  }
  if (contador) {
    contador.textContent = `${checkboxes.length} produto(s) selecionado(s)`;
  }
}

document.querySelectorAll('.chk-produto').forEach(chk => {
  chk.addEventListener('change', atualizarBotaoCarrinho);
});

document.getElementById('btn-adicionar-carrinho')?.addEventListener('click', async () => {
  const selecionados = [...document.querySelectorAll('.chk-produto:checked')]
    .map(chk => parseInt(chk.value));

  if (selecionados.length === 0) {
    alert('Selecione pelo menos um produto.');
    return;
  }

  const cestaId = window.CESTA_ATIVA_ID;
  if (!cestaId) {
    alert('Você precisa ter uma cesta ativa. Crie uma em "Gerenciar Cestas".');
    return;
  }

  const resposta = await fetch('/api/cesta.php?acao=adicionarItens', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      cesta_id:    cestaId,
      produto_ids: selecionados,
      csrf_token:  getCsrfToken(),
    }),
  });

  const json = await resposta.json();
  if (json.sucesso) {
    alert(`${selecionados.length} produto(s) adicionado(s) à cesta!`);
    window.location.href = '/views/carrinho/index.php';
  } else {
    alert('Erro ao adicionar produtos.');
  }
}); 
