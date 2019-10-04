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
    //document.getElementById(registro).style.color="lightblue";
    //document.getElementById(registro).style.background ="lightblue";
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

            // Verificar se a data impostada não é anterior a última data de relatório
            var $familia = document.getElementById('cd_fml').value;

            $.getJSON('ConsultaCbDependente.php?opcao=maxDataAcomp&valor='+$familia, 
              function (dados) {

               if (dados.length > 0){    
                  $.each(dados, function(i, obj){

                    var data_invertida = data.substring(6)+data.substring(3,5)+data.substring(0,2);
                    var dt_acomp_invertida = obj.dt_acomp.substring(6)+obj.dt_acomp.substring(3,5)+obj.dt_acomp.substring(0,2);

                    if (data_invertida < dt_acomp_invertida) {
                      var mes_ano_data = data.substring(3);
                      var mes_ano_dt_acomp = obj.dt_acomp.substring(3);

                      // Para verificar se é no mesmo mês/ano, pois pode ter havido erro na digitação inicial
                      if (mes_ano_data == mes_ano_dt_acomp) {
                        enviaAlert();  
                      } else {
                        enviaAlert1();
                      }
                    } else {
                      enviaAlert();
                    }
                  })
                } else {
                  enviaAlert();                  
                }
              })
          }
        }
      } catch {
        enviaAlert();
      }
    }
  }
}); 

function enviaAlert() {
  swal({
      title: '',
      text: 'Confirma a inclusão?',
      type: 'warning',                  //"warning", "error", "success", "info"
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#D80C1B',
      confirmButtonText: 'Sim',
      cancelButtonText: 'Não',
      closeOnClickOutside: false

    }, function(isConfirm) {

      if (isConfirm) {
        $("#FormIncluir").submit();
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


