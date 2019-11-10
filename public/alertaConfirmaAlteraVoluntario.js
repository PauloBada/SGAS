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
    //document.getElementById(registro).style.color="lightblue";
    //document.getElementById(registro).style.background ="lightblue";
    document.getElementById(registro).style.borderColor = "#ff0000";

  } else {
    var cpf = document.getElementById('cpf').value;

    if (cpf != '') {
      valida_cpf = validaCPF(cpf);

      if (valida_cpf != 0) {
       swal('', 'CPF inválido!', 'warning');
       document.getElementById('cpf').focus(); 
       document.getElementById('cpf').style.borderColor = "#ff0000";
      
      } else {
        enviaAlert();     
      }
    } else {
      enviaAlert();     
    }
  }
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
        $("#FormAltera").submit();
      }
  })
}

// ========================================================================== 

function validaCPF($cpf) {
   if (!$cpf.match(/[0-9]/)) {
       return 1;
   }

   $cpf = "00000000000" + $cpf.replace(/\D/g,'');
   $cpf = $cpf.slice(-11,-1) + $cpf.slice(-1);
   
   if ($cpf.length != 11) {     
     return 2;
   }
   
   if ($cpf == '00000000000' || 
       $cpf == '11111111111' || 
       $cpf == '22222222222' || 
       $cpf == '33333333333' || 
       $cpf == '44444444444' || 
       $cpf == '55555555555' || 
       $cpf == '66666666666' || 
       $cpf == '77777777777' || 
       $cpf == '88888888888' || 
       $cpf == '99999999999') {
       return 3;
    }
        
   for ($t = 9; $t < 11; $t++) {
       for ($d = 0, $c = 0; $c < $t; $c++) {
           $d += $cpf[$c] * (($t + 1) - $c);
       }

       $d = ((10 * $d) % 11) % 10;
       
       if ($cpf[$c] != $d) {
           return 4;
       }
   }

   return 0;      
}

