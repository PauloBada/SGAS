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
    // Testar se data é válida //
    try {
      if (document.getElementById('dataFormulario').value != '') {
        var data = document.getElementById('dataFormulario').value;

        var patternValidaData = /^(((0[1-9]|[12][0-9]|3[01])([-.\/])(0[13578]|10|12)([-.\/])(\d{4}))|(([0][1-9]|[12][0-9]|30)([-.\/])(0[469]|11)([-.\/])(\d{4}))|((0[1-9]|1[0-9]|2[0-8])([-.\/])(02)([-.\/])(\d{4}))|((29)(\.|-|\/)(02)([-.\/])([02468][048]00))|((29)([-.\/])(02)([-.\/])([13579][26]00))|((29)([-.\/])(02)([-.\/])([0-9][0-9][0][48]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][2468][048]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][13579][26])))$/;

        if(!patternValidaData.test(data)){
          swal('', 'Data Inválida!', 'warning');
          document.getElementById('dataFormulario').focus(); 
          document.getElementById('dataFormulario').style.borderColor = "#ff0000";
        } else {
          enviaAlert();
        }
      } else {
        enviaAlert();
      }
    } catch {
      try {
        if (document.getElementById('dataFormularioN').value == '') {
          swal('', 'Campo obrigatório * sem Preenchimento!', 'warning');
          document.getElementById('dataFormularioN').focus(); 
          document.getElementById('dataFormularioN').style.borderColor = "#ff0000";
        } else {
          var data = document.getElementById('dataFormularioN').value;

          var patternValidaData = /^(((0[1-9]|[12][0-9]|3[01])([-.\/])(0[13578]|10|12)([-.\/])(\d{4}))|(([0][1-9]|[12][0-9]|30)([-.\/])(0[469]|11)([-.\/])(\d{4}))|((0[1-9]|1[0-9]|2[0-8])([-.\/])(02)([-.\/])(\d{4}))|((29)(\.|-|\/)(02)([-.\/])([02468][048]00))|((29)([-.\/])(02)([-.\/])([13579][26]00))|((29)([-.\/])(02)([-.\/])([0-9][0-9][0][48]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][2468][048]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][13579][26])))$/;

          if(!patternValidaData.test(data)){
            swal('', 'Data Inválida!', 'warning');
            document.getElementById('dataFormularioN').focus(); 
            document.getElementById('dataFormularioN').style.borderColor = "#ff0000";
          } else {
            enviaAlert();  
          }
        }
      } catch {
        enviaAlert();  
      }
    }
  }
}); 

$("#btnFormalizar").click(function(){
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
    // Testar se data é válida //
    try {
      if (document.getElementById('dataFormulario').value != '') {
        var data = document.getElementById('dataFormulario').value;

        var patternValidaData = /^(((0[1-9]|[12][0-9]|3[01])([-.\/])(0[13578]|10|12)([-.\/])(\d{4}))|(([0][1-9]|[12][0-9]|30)([-.\/])(0[469]|11)([-.\/])(\d{4}))|((0[1-9]|1[0-9]|2[0-8])([-.\/])(02)([-.\/])(\d{4}))|((29)(\.|-|\/)(02)([-.\/])([02468][048]00))|((29)([-.\/])(02)([-.\/])([13579][26]00))|((29)([-.\/])(02)([-.\/])([0-9][0-9][0][48]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][2468][048]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][13579][26])))$/;

        if(!patternValidaData.test(data)){
          swal('', 'Data Inválida!', 'warning');
          document.getElementById('dataFormulario').focus(); 
          document.getElementById('dataFormulario').style.borderColor = "#ff0000";
        } else {
          enviaAlert1();
        }
      } else {
        enviaAlert1();
      }
    } catch {
      try {
        if (document.getElementById('dataFormularioN').value == '') {
          swal('', 'Campo obrigatório * sem Preenchimento!', 'warning');
          document.getElementById('dataFormularioN').focus(); 
          document.getElementById('dataFormularioN').style.borderColor = "#ff0000";
        } else {
          var data = document.getElementById('dataFormularioN').value;

          var patternValidaData = /^(((0[1-9]|[12][0-9]|3[01])([-.\/])(0[13578]|10|12)([-.\/])(\d{4}))|(([0][1-9]|[12][0-9]|30)([-.\/])(0[469]|11)([-.\/])(\d{4}))|((0[1-9]|1[0-9]|2[0-8])([-.\/])(02)([-.\/])(\d{4}))|((29)(\.|-|\/)(02)([-.\/])([02468][048]00))|((29)([-.\/])(02)([-.\/])([13579][26]00))|((29)([-.\/])(02)([-.\/])([0-9][0-9][0][48]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][2468][048]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][13579][26])))$/;

          if(!patternValidaData.test(data)){
            swal('', 'Data Inválida!', 'warning');
            document.getElementById('dataFormularioN').focus(); 
            document.getElementById('dataFormularioN').style.borderColor = "#ff0000";
          } else {
            enviaAlert1();  
          }
        }
      } catch {
        enviaAlert1();         
      }
    }
  }
}); 


function enviaAlert() {
  swal({
      title: '',
      text: 'Confirma a atualização do relatório de Desligamento?',
      type: 'warning',                  //"warning", "error", "success", "info"
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#D80C1B',
      confirmButtonText: 'Sim',
      cancelButtonText: 'Não',
      closeOnClickOutside: false

    }, function(isConfirm) {

      if (isConfirm) {
        //$("#FormAltera");
        $('#FormAltera').attr('action', '/fAConcluirRDBaseAtualiza').submit();
      }
  })
}

function enviaAlert1() {
  swal({
      title: '',
      text: 'Confirma a formalização do relatório de Desligamento?',
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
        $('#FormAltera').attr('action', '/fAConcluirRDBaseFormaliza').submit();
      }
  })
}

