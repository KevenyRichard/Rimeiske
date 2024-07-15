    let menu = document.querySelector('#tb-menu');
    let menuArea = document.querySelector('#header');
    let container = document.querySelector('#container');
    let videoIntrodution = document.querySelector('.video_introducao iframe');
    let textIntrodution = document.querySelector('.texto_introducao');
    let footer = document.querySelector('#footer');
    let icons = document.querySelector('.icons');
    let copyright = document.querySelector('#footer .copyright');
function menuAnimated(x) {
    x.classList.toggle("change");
    if (menuArea.classList.contains('open')) {
        menuArea.classList.remove('open');
        menu.classList.remove('open');
        container.classList.remove('opened');
        footer.classList.remove('open');
        icons.classList.remove('open');
        copyright.classList.remove('open');
        videoIntrodution.classList.remove('open');
        textIntrodution.classList.remove('open');
    }
    else {
        menuArea.classList.add('open');
        container.classList.add('opened');
        menu.classList.add('open');
        footer.classList.add('open');
        icons.classList.add('open');
        copyright.classList.add('open');
        videoIntrodution.classList.add('open');
        textIntrodution.classList.add('open');
    }
}

let boxAlert = document.querySelector('#box-alerta');
// Função para verificar se a sessão foi atualizada a cada segundo
function checkSession() {
    fetch('menu.php') // Arquivo PHP para verificar a sessão (veja as respostas anteriores)
        .then(response => response.json())
        .then(data => {
            if (data.alert) {
                boxAlert.classList.remove('close');
                let divMensagem = document.querySelector('#magic-box .alertas .mensagem p');
                divMensagem.textContent = data.alert;
            }
        });

    setTimeout(checkSession, 1000); // Verifica a sessão a cada segundo
}

checkSession(); // Inicia a verificação da sessão

function closeAlert() {
    boxAlert.classList.add('close');
}


// Função para mostrar o <select> correspondente com base na seleção do rádio
function mostrarSelect(dptoSelecionado) {
    // Ocultar todos os <select>
    const todosSelects = document.querySelectorAll('.cp_local');
    todosSelects.forEach(function(select) {
      select.style.display = 'none';

    });
    // Ocultar todos os <label>
    const todosLabel = document.querySelectorAll('.cp_serv');
    todosLabel.forEach(function(label) {
      label.style.display = 'none';
      
    });

    // Mostrar o <select> correspondente à opção selecionada
    const selectCorrespondente = document.getElementById('local_serv_' + dptoSelecionado);
    const labelCorrespondente = document.getElementById('label_serv_' + dptoSelecionado);
    if (selectCorrespondente) {
      selectCorrespondente.style.display = 'block';
    }
    if (labelCorrespondente) {
        labelCorrespondente.style.display = 'block';
    }
  }

  // Adicionar um listener de evento aos radios para chamar a função quando selecionado
  const radios = document.querySelectorAll('.campo_dpto');
  radios.forEach(function(radio) {
    radio.addEventListener('change', function() {
      if (radio.checked) {
        const dptoSelecionado = radio.value;
        mostrarSelect(dptoSelecionado);
      }
    });
  });
