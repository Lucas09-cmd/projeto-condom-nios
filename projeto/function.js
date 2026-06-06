/**
 * function.js
 * Utilitários de validação de formulário.
 * NOTA: novasolicitacao() foi removida daqui — a versão correta
 * com chamada à API está diretamente em solicitacoes.html.
 */

function setBorder(id, value) {
  const el = document.getElementById(id);
  if (!el) return;
  el.style.border = value;
}

/**
 * Valida campos de senha e e-mail no formulário de cadastro.
 * Utilizada apenas em páginas que importam este script com os
 * IDs: "senha", "senha_confirm", "login".
 */
function atividade() {
  const senhaEl       = document.getElementById('senha');
  const confirmacaoEl = document.getElementById('senha_confirm');
  const emailEl       = document.getElementById('login');

  if (!senhaEl || !confirmacaoEl || !emailEl) return;

  const senha       = senhaEl.value.trim();
  const confirmacao = confirmacaoEl.value.trim();
  const email       = emailEl.value.trim();

  setBorder('senha', '');
  setBorder('senha_confirm', '');
  setBorder('login', '');

  if (senha.length < 8) {
    alert('A senha deve conter pelo menos 8 caracteres!');
    setBorder('senha', '2px solid red');
    return;
  }

  if (senha !== confirmacao) {
    alert('As senhas não coincidem!');
    setBorder('senha', '2px solid red');
    setBorder('senha_confirm', '2px solid red');
    return;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    alert('E-mail inválido!');
    setBorder('login', '2px solid red');
    return;
  }

  alert('Senhas coincidem! E-mail válido!');
}

// Listener do botão de submit (se existir na página)
const submitButton = document.getElementById('submit');
if (submitButton) {
  submitButton.addEventListener('click', function (event) {
    event.preventDefault();
    atividade();
  });
}

// Limpa borda vermelha ao digitar novamente
const senhaInput   = document.getElementById('senha');
const confirmInput = document.getElementById('senha_confirm');

if (senhaInput) {
  senhaInput.addEventListener('input', function () {
    setBorder('senha', '');
  });
}

if (confirmInput) {
  confirmInput.addEventListener('input', function () {
    setBorder('senha_confirm', '');
  });
}
