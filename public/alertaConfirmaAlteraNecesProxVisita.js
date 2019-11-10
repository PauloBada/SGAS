/* Javascript para Confirmação de Inclusão de dados */

$("#btnAlterar").click(function(){

  // Poder repassar os valores por $_POST, caso estejam "disabled"
  //$("#cd_setor_resp").attr("disabled", false);
  //$("#cd_tip_unid_item").attr("disabled", false);
  //$("#dinheiro").attr("disabled", false);

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
    //document.getElementById(registro).style.color="lightblue";
    //document.getElementById(registro).style.background ="lightblue";
    document.getElementById(registro).style.borderColor = "#ff0000";

  } else {
    // Testar se valor não é zero
    if (document.getElementById('3').value != '') {
      var qtde = document.getElementById('3').value;

      if(qtde <= 0){
        swal('', 'Quantidade tem que ser maior do que zero!', 'warning');
        document.getElementById('3').focus(); 
        document.getElementById('3').style.borderColor = "#ff0000";
      } else {
        enviaAlert();
      }
    } 
  }
}); 

$("#btnExcluir").click(function(){
   enviaAlertExclui();
}); 


function enviaAlert() {
  swal({
      title: '',
      text: 'Confirma a alteração?',
      type: 'warning',                  //"warning", "error", "success", "info"
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#D80C1B',
      confirmButtonText: 'Sim',
      cancelButtonText: 'Não',
      closeOnClickOutside: false

    }, function(isConfirm) {

      if (isConfirm) {
        $('#FormAlterar').attr('action', '/fNAlterarNecesProxVisitaBase').submit();
      }
  })
}

function enviaAlert1($dt_acomp) {
  swal({
      title: '',
      text: 'Erro. Data Informada é Anterior a Data do último Acompanhamento!',
      type: 'warning',                  //"warning", "error", "success", "info"
      showCancelButton: false,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#D80C1B',
      confirmButtonText: 'Ok',
      closeOnClickOutside: false

    })
}

function enviaAlertExclui() {
  swal({
      title: '',
      text: 'Confirma a exclusão?',
      type: 'warning',                  //"warning", "error", "success", "info"
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#D80C1B',
      confirmButtonText: 'Sim',
      cancelButtonText: 'Não',
      closeOnClickOutside: false

    }, function(isConfirm) {

      if (isConfirm) {
        //$("#FormAlterar").submit();
        $('#FormAlterar').attr('action', '/fNExcluirNecesBase').submit();
      }
  })
}

