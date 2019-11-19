/* Javascript para Confirmação de Inclusão de dados */

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

      // Validação de data
      if (document.getElementById('dt_recur_orc').value != '') {
        var data = document.getElementById('dt_recur_orc').value;

        var patternValidaData = /^(((0[1-9]|[12][0-9]|3[01])([-.\/])(0[13578]|10|12)([-.\/])(\d{4}))|(([0][1-9]|[12][0-9]|30)([-.\/])(0[469]|11)([-.\/])(\d{4}))|((0[1-9]|1[0-9]|2[0-8])([-.\/])(02)([-.\/])(\d{4}))|((29)(\.|-|\/)(02)([-.\/])([02468][048]00))|((29)([-.\/])(02)([-.\/])([13579][26]00))|((29)([-.\/])(02)([-.\/])([0-9][0-9][0][48]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][2468][048]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][13579][26])))$/;

        if(!patternValidaData.test(data)){
          swal('', 'Data Inválida!', 'warning');
          document.getElementById('dt_recur_orc').focus(); 
          document.getElementById('dt_recur_orc').style.borderColor = "#ff0000";

        } else {
          // Verifica se a data do Recuros náo é inferior a uma semana (7 dias)                   
          var ano = document.getElementById('dt_recur_orc').value.substr(6, 4);
          
          var mes = document.getElementById('dt_recur_orc').value.substr(3, 2);
          // Month ==> 0 a 11 (0-janeiro; 11-Dezembro). 
          mes = mes - 1;
          
          var dia = document.getElementById('dt_recur_orc').value.substr(0, 2);
          
          var dataRecurso = new Date(ano, mes, dia);

          // Data e hora atuais
          var data7dias = new Date();
          // Retroage 7 dias da data atual para comparar com a data impostada
          data7dias.setDate(data7dias.getDate() - 7);

          if(dataRecurso < data7dias) {
            swal('', 'Data Recurso inferior a uma semana!', 'warning');
            document.getElementById('dt_recur_orc').focus(); 
            document.getElementById('dt_recur_orc').style.borderColor = "#ff0000";

          } else {
            enviaAlert();            
          }
        }
      } else {
          swal('', 'Data Inválida!', 'warning');
          document.getElementById('dt_recur_orc').focus(); 
          document.getElementById('dt_recur_orc').style.borderColor = "#ff0000";
      }
    }
  }
}); 

$("#btnCancelar").click(function(){
      enviaAlert1();  
});


function enviaAlert() {
  swal({
      title: '',
      text: 'Confirma a atualização do Recurso?',
      type: 'warning',                  //"warning", "error", "success", "info"
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#D80C1B',
      confirmButtonText: 'Sim',
      cancelButtonText: 'Não',
      closeOnClickOutside: false

    }, function(isConfirm) {

      if (isConfirm) {
        $('#FormAltera').attr('action', '/recFinanDPSRecursoRealGerenciaAlteraBase').submit();
      }
  })
}

function enviaAlert1() {
  swal({
      title: '',
      text: 'Confirma o cancelamento do Recurso?',
      type: 'warning',                  //"warning", "error", "success", "info"
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#D80C1B',
      confirmButtonText: 'Sim',
      cancelButtonText: 'Não',
      closeOnClickOutside: false

    }, function(isConfirm) {

      if (isConfirm) {
        $('#FormAltera').attr('action', '/recFinanDPSRecursoRealGerenciaCancelaBase').submit();
      }
  })
}
