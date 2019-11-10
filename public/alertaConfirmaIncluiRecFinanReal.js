/* Javascript para Confirmação de Inclusão de dados */

$("#btnIncluir").click(function(){
  var cont = 0;
  var campo_vazio = 0;

  for (var d = 1; d < 100; d++) {
    try { 
      cont++;
    } catch { 
      break;
    }
  }

  for (var dado = 1; dado <= cont; dado++) {
    var dados = dado.toString();

    try {
      if (document.getElementById(dados).value == '') {
        campo_vazio = dado;
        break;
      } 
    } catch {
      break;
    }
  }

  if (campo_vazio > 0 ) {
    var registro = campo_vazio.toString();

    swal('', 'Campo obrigatório * sem Preenchimento!', 'warning');

    document.getElementById(registro).focus(); 
    document.getElementById(registro).style.borderColor = "#ff0000";

  } else {

    var valor = document.getElementById('dinheiro').value;

    if (valor == '') {
      valor = 0;
    } else {
      valor = valor.replace(".", "");
      valor = valor.replace(",", ".");
    }

    //if (document.getElementById('dinheiro').value == '' || document.getElementById('dinheiro').value <= 0) {
    if (valor <= 0) {
      swal('', 'Valor sem Preenchimento ou com Valor Zero!', 'warning');
      document.getElementById('dinheiro').focus(); 
      document.getElementById('dinheiro').style.borderColor = "#ff0000";
    
    } else {
      enviaAlert();  
    }
  }
}); 


function enviaAlert() {
  swal({
      title: '',
      text: 'Confirma a inclusão do Recurso?',
      type: 'warning',                  //"warning", "error", "success", "info"
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#D80C1B',
      confirmButtonText: 'Sim',
      cancelButtonText: 'Não',
      closeOnClickOutside: false

    }, function(isConfirm) {

      if (isConfirm) {
        $('#FormAltera').attr('action', '/recFinanDPSRecursoRealIncluiBase').submit();
      }
  })
}

