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

$("#btnAlterar").click(function(){
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
      enviaAlert1();  
    }
  }
}); 

$("#btnCancelar").click(function(){
      enviaAlert2();  
});

$("#btnConcluir").click(function(){
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
      enviaAlert3();  
    }
  }
}); 

$("#btnAutorizar").click(function(){
      enviaAlert4();  
});

$("#btnCancelarAutorizacao").click(function(){
      enviaAlert5();  
});

$("#btnCancelarSolicitacao").click(function(){
      enviaAlert6();  
});

function enviaAlert() {
  swal({
      title: '',
      text: 'Confirma a inclusão da Solicitação?',
      type: 'warning',                  //"warning", "error", "success", "info"
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#D80C1B',
      confirmButtonText: 'Sim',
      cancelButtonText: 'Não',
      closeOnClickOutside: false

    }, function(isConfirm) {

      if (isConfirm) {
        $('#FormAltera').attr('action', '/recFinanFamiliaSolicitarIncluirBase').submit();
      }
  })
}

function enviaAlert1() {
  swal({
      title: '',
      text: 'Confirma a atualização da Solicitação?',
      type: 'warning',                  //"warning", "error", "success", "info"
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#D80C1B',
      confirmButtonText: 'Sim',
      cancelButtonText: 'Não',
      closeOnClickOutside: false

    }, function(isConfirm) {

      if (isConfirm) {
        $('#FormAltera').attr('action', '/recFinanFamiliaSolicitarAtualizarBase').submit();
      }
  })
}

function enviaAlert2() {
  swal({
      title: '',
      text: 'Confirma o cancelamento da Solicitação?',
      type: 'warning',                  //"warning", "error", "success", "info"
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#D80C1B',
      confirmButtonText: 'Sim',
      cancelButtonText: 'Não',
      closeOnClickOutside: false

    }, function(isConfirm) {

      if (isConfirm) {
        $('#FormAltera').attr('action', '/recFinanFamiliaSolicitarCancelarBase').submit();
      }
  })
}

function enviaAlert3() {
  swal({
      title: '',
      text: 'Confirma o envio da Solicitação para Autorização?',
      type: 'warning',                  //"warning", "error", "success", "info"
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#D80C1B',
      confirmButtonText: 'Sim',
      cancelButtonText: 'Não',
      closeOnClickOutside: false

    }, function(isConfirm) {

      if (isConfirm) {
        //$("#FormAltera").submit();
        $('#FormAltera').attr('action', '/recFinanFamiliaSolicitarConcluirBase').submit();
      }
  })
}

function enviaAlert4() {
  swal({
      title: '',
      text: 'Confirma a Autorização do Pedido?',
      type: 'warning',                  //"warning", "error", "success", "info"
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#D80C1B',
      confirmButtonText: 'Sim',
      cancelButtonText: 'Não',
      closeOnClickOutside: false

    }, function(isConfirm) {

      if (isConfirm) {
        $('#FormAltera').attr('action', '/recFinanDPSGerenciaAutorizacaoBase').submit();
      }
  })
}

function enviaAlert5() {
  swal({
      title: '',
      text: 'Confirma o cancelamento da Autorização?',
      type: 'warning',                  //"warning", "error", "success", "info"
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#D80C1B',
      confirmButtonText: 'Sim',
      cancelButtonText: 'Não',
      closeOnClickOutside: false

    }, function(isConfirm) {

      if (isConfirm) {
        $('#FormAltera').attr('action', '/recFinanDPSGerenciaCancelaAutorizacaoBase').submit();
      }                                   
  })
}

function enviaAlert6() {
  swal({
      title: '',
      text: 'Confirma o cancelamento da Solicitação?',
      type: 'warning',                  //"warning", "error", "success", "info"
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#D80C1B',
      confirmButtonText: 'Sim',
      cancelButtonText: 'Não',
      closeOnClickOutside: false

    }, function(isConfirm) {

      if (isConfirm) {
        $('#FormAltera').attr('action', '/recFinanDPSGerenciaCancelaSolicitacaoBase').submit();
      }                                   
  })
}


