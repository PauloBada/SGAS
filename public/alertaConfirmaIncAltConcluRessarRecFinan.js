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

    var data = document.getElementById('dt_doc_ressar').value;
    var bco = document.getElementById('1').value;
    var age = document.getElementById('2').value;
    var cta = document.getElementById('3').value;
    var dig_cta = document.getElementById('4').value;
    var cpf = document.getElementById('cpf_cred_ressar').value;
    var cnpj = document.getElementById('cnpj_cred_ressar').value;

    if (bco == '') {
      bco = 0;
    } 

    if (age == '') {
      age = 0;
    } 

    if (cta == '') {
      cta = 0;
    } 

    if (cpf == '') {
      cpf = 0;
    } else {
     cpf = cpf.replace('.', '');
     cpf = cpf.replace('-', '');
    }

    if (cnpj == '') {
      cnpj = 0;
    } else {
     cnpj = cnpj.replace('.', '');
     cnpj = cnpj.replace('-', '');
     cnpj = cnpj.replace('/', '');
    }

    var patternValidaData = /^(((0[1-9]|[12][0-9]|3[01])([-.\/])(0[13578]|10|12)([-.\/])(\d{4}))|(([0][1-9]|[12][0-9]|30)([-.\/])(0[469]|11)([-.\/])(\d{4}))|((0[1-9]|1[0-9]|2[0-8])([-.\/])(02)([-.\/])(\d{4}))|((29)(\.|-|\/)(02)([-.\/])([02468][048]00))|((29)([-.\/])(02)([-.\/])([13579][26]00))|((29)([-.\/])(02)([-.\/])([0-9][0-9][0][48]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][2468][048]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][13579][26])))$/;

    if(!patternValidaData.test(data)){
      swal('', 'Data do Documento Inválida!', 'warning');
      document.getElementById('dt_doc_ressar').focus(); 
      document.getElementById('dt_doc_ressar').style.borderColor = "#ff0000";

    } else if (bco <= 0) {
      swal('', 'Banco com Valor Zero!', 'warning');
      document.getElementById('1').focus(); 
      document.getElementById('1').style.borderColor = "#ff0000";
    
    } else if(age <= 0) {
      swal('', 'Agência com Valor Zero!', 'warning');
      document.getElementById('2').focus(); 
      document.getElementById('2').style.borderColor = "#ff0000";

    } else if (cta <= 0) {
      swal('', 'Conta com Valor Zero!', 'warning');
      document.getElementById('3').focus(); 
      document.getElementById('3').style.borderColor = "#ff0000";

    } else if (dig_cta == '') {
      swal('', 'Dígito da Conta sem Valor!', 'warning');
      document.getElementById('3').focus(); 
      document.getElementById('3').style.borderColor = "#ff0000";

    } else if (cpf == 0 && cnpj == 0) {
      swal('', 'CPF ou CNPJ precisam ser preenchidos!', 'warning');
      document.getElementById('cpf_cred_ressar').focus(); 
      document.getElementById('cpf_cred_ressar').style.borderColor = "#ff0000";
     
     } else if (cpf > 0 && cnpj > 0) {
      swal('', 'Preencha apenas CPF ou apenas CNPJ!', 'warning');
      document.getElementById('cpf_cred_ressar').focus(); 
      document.getElementById('cpf_cred_ressar').style.borderColor = "#ff0000";
    
    } else {

      if (cpf > 0) {
        valida_cpf = validaCPF(cpf);

        if (valida_cpf != 0) {
         swal('', 'CPF inválido!', 'warning');
         document.getElementById('cpf_cred_ressar').focus(); 
         document.getElementById('cpf_cred_ressar').style.borderColor = "#ff0000";
        
        } else {
          enviaAlert();     
        }
      }

      if (cnpj > 0) {
        valida_cnpj = validaCNPJ(cnpj);

        if (valida_cnpj != 0) {
         swal('', 'CNPJ inválido!', 'warning');
         document.getElementById('cnpj_cred_ressar').focus(); 
         document.getElementById('cnpj_cred_ressar').style.borderColor = "#ff0000";
        
        } else {
          enviaAlert();     
        }
      }
    }
  }
}); 

// ========================================================================== 

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

    var data = document.getElementById('dt_doc_ressar').value;
    var bco = document.getElementById('1').value;
    var age = document.getElementById('2').value;
    var cta = document.getElementById('3').value;
    var dig_cta = document.getElementById('4').value;
    var cpf = document.getElementById('cpf_cred_ressar').value;
    var cnpj = document.getElementById('cnpj_cred_ressar').value;

    if (bco == '') {
      bco = 0;
    } 

    if (age == '') {
      age = 0;
    } 

    if (cta == '') {
      cta = 0;
    } 

    if (cpf == '') {
      cpf = 0;
    } else {
     cpf = cpf.replace('.', '');
     cpf = cpf.replace('-', '');
    }

    if (cnpj == '') {
      cnpj = 0;
    } else {
     cnpj = cnpj.replace('.', '');
     cnpj = cnpj.replace('-', '');
     cnpj = cnpj.replace('/', '');
    }

    var patternValidaData = /^(((0[1-9]|[12][0-9]|3[01])([-.\/])(0[13578]|10|12)([-.\/])(\d{4}))|(([0][1-9]|[12][0-9]|30)([-.\/])(0[469]|11)([-.\/])(\d{4}))|((0[1-9]|1[0-9]|2[0-8])([-.\/])(02)([-.\/])(\d{4}))|((29)(\.|-|\/)(02)([-.\/])([02468][048]00))|((29)([-.\/])(02)([-.\/])([13579][26]00))|((29)([-.\/])(02)([-.\/])([0-9][0-9][0][48]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][2468][048]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][13579][26])))$/;

    if(!patternValidaData.test(data)){
      swal('', 'Data do Documento Inválida!', 'warning');
      document.getElementById('dt_doc_ressar').focus(); 
      document.getElementById('dt_doc_ressar').style.borderColor = "#ff0000";

    } else if (bco <= 0) {
      swal('', 'Banco com Valor Zero!', 'warning');
      document.getElementById('1').focus(); 
      document.getElementById('1').style.borderColor = "#ff0000";
    
    } else if(age <= 0) {
      swal('', 'Agência com Valor Zero!', 'warning');
      document.getElementById('2').focus(); 
      document.getElementById('2').style.borderColor = "#ff0000";

    } else if (cta <= 0) {
      swal('', 'Conta com Valor Zero!', 'warning');
      document.getElementById('3').focus(); 
      document.getElementById('3').style.borderColor = "#ff0000";

    } else if (dig_cta == '') {
      swal('', 'Dígito da Conta sem Valor!', 'warning');
      document.getElementById('3').focus(); 
      document.getElementById('3').style.borderColor = "#ff0000";

    } else if (cpf == 0 && cnpj == 0) {
      swal('', 'CPF ou CNPJ precisam ser preenchidos!', 'warning');
      document.getElementById('cpf_cred_ressar').focus(); 
      document.getElementById('cpf_cred_ressar').style.borderColor = "#ff0000";
     
     } else if (cpf > 0 && cnpj > 0) {
      swal('', 'Preencha apenas CPF ou apenas CNPJ!', 'warning');
      document.getElementById('cpf_cred_ressar').focus(); 
      document.getElementById('cpf_cred_ressar').style.borderColor = "#ff0000";
    
    } else {

      if (cpf > 0) {
        valida_cpf = validaCPF(cpf);

        if (valida_cpf != 0) {
         swal('', 'CPF inválido!', 'warning');
         document.getElementById('cpf_cred_ressar').focus(); 
         document.getElementById('cpf_cred_ressar').style.borderColor = "#ff0000";
        
        } else {
          enviaAlert1();     
        }
      }

      if (cnpj > 0) {
        valida_cnpj = validaCNPJ(cnpj);

        if (valida_cnpj != 0) {
         swal('', 'CNPJ inválido!', 'warning');
         document.getElementById('cnpj_cred_ressar').focus(); 
         document.getElementById('cnpj_cred_ressar').style.borderColor = "#ff0000";
        
        } else {
          enviaAlert1();     
        }
      }
    }
  }
}); 


// ========================================================================== 

$("#btnCancelar").click(function(){
    enviaAlert2();  
});


// ========================================================================== 

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

    var data = document.getElementById('dt_doc_ressar').value;
    var bco = document.getElementById('1').value;
    var age = document.getElementById('2').value;
    var cta = document.getElementById('3').value;
    var dig_cta = document.getElementById('4').value;
    var cpf = document.getElementById('cpf_cred_ressar').value;
    var cnpj = document.getElementById('cnpj_cred_ressar').value;

    if (bco == '') {
      bco = 0;
    } 

    if (age == '') {
      age = 0;
    } 

    if (cta == '') {
      cta = 0;
    } 

    if (cpf == '') {
      cpf = 0;
    } else {
     cpf = cpf.replace('.', '');
     cpf = cpf.replace('-', '');
    }

    if (cnpj == '') {
      cnpj = 0;
    } else {
     cnpj = cnpj.replace('.', '');
     cnpj = cnpj.replace('-', '');
     cnpj = cnpj.replace('/', '');
    }

    var patternValidaData = /^(((0[1-9]|[12][0-9]|3[01])([-.\/])(0[13578]|10|12)([-.\/])(\d{4}))|(([0][1-9]|[12][0-9]|30)([-.\/])(0[469]|11)([-.\/])(\d{4}))|((0[1-9]|1[0-9]|2[0-8])([-.\/])(02)([-.\/])(\d{4}))|((29)(\.|-|\/)(02)([-.\/])([02468][048]00))|((29)([-.\/])(02)([-.\/])([13579][26]00))|((29)([-.\/])(02)([-.\/])([0-9][0-9][0][48]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][2468][048]))|((29)([-.\/])(02)([-.\/])([0-9][0-9][13579][26])))$/;

    if(!patternValidaData.test(data)){
      swal('', 'Data do Documento Inválida!', 'warning');
      document.getElementById('dt_doc_ressar').focus(); 
      document.getElementById('dt_doc_ressar').style.borderColor = "#ff0000";

    } else if (bco <= 0) {
      swal('', 'Banco com Valor Zero!', 'warning');
      document.getElementById('1').focus(); 
      document.getElementById('1').style.borderColor = "#ff0000";
    
    } else if(age <= 0) {
      swal('', 'Agência com Valor Zero!', 'warning');
      document.getElementById('2').focus(); 
      document.getElementById('2').style.borderColor = "#ff0000";

    } else if (cta <= 0) {
      swal('', 'Conta com Valor Zero!', 'warning');
      document.getElementById('3').focus(); 
      document.getElementById('3').style.borderColor = "#ff0000";

    } else if (dig_cta == '') {
      swal('', 'Dígito da Conta sem Valor!', 'warning');
      document.getElementById('3').focus(); 
      document.getElementById('3').style.borderColor = "#ff0000";

    } else if (cpf == 0 && cnpj == 0) {
      swal('', 'CPF ou CNPJ precisam ser preenchidos!', 'warning');
      document.getElementById('cpf_cred_ressar').focus(); 
      document.getElementById('cpf_cred_ressar').style.borderColor = "#ff0000";
     
     } else if (cpf > 0 && cnpj > 0) {
      swal('', 'Preencha apenas CPF ou apenas CNPJ!', 'warning');
      document.getElementById('cpf_cred_ressar').focus(); 
      document.getElementById('cpf_cred_ressar').style.borderColor = "#ff0000";
    
    } else {

      if (cpf > 0) {
        valida_cpf = validaCPF(cpf);

        if (valida_cpf != 0) {
         swal('', 'CPF inválido!', 'warning');
         document.getElementById('cpf_cred_ressar').focus(); 
         document.getElementById('cpf_cred_ressar').style.borderColor = "#ff0000";
        
        } else {
          enviaAlert3();     
        }
      }

      if (cnpj > 0) {
        valida_cnpj = validaCNPJ(cnpj);

        if (valida_cnpj != 0) {
         swal('', 'CNPJ inválido!', 'warning');
         document.getElementById('cnpj_cred_ressar').focus(); 
         document.getElementById('cnpj_cred_ressar').style.borderColor = "#ff0000";
        
        } else {
          enviaAlert3();     
        }
      }
    }
  }
}); 


// ========================================================================== 

function enviaAlert() {
  swal({
      title: '',
      text: 'Confirma a inclusão da Solicitação de Ressarcimento?',
      type: 'warning',                  //"warning", "error", "success", "info"
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#D80C1B',
      confirmButtonText: 'Sim',
      cancelButtonText: 'Não',
      closeOnClickOutside: false

    }, function(isConfirm) {

      if (isConfirm) {
        $('#FormAltera').attr('action', '/recFinanDPSGerenciaRessarIncluirBase').submit();
      }
  })
}

// ========================================================================== 

function enviaAlert1() {
  swal({
      title: '',
      text: 'Confirma a atualização da Solicitação de Ressarcimento?',
      type: 'warning',                  //"warning", "error", "success", "info"
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#D80C1B',
      confirmButtonText: 'Sim',
      cancelButtonText: 'Não',
      closeOnClickOutside: false

    }, function(isConfirm) {

      if (isConfirm) {
        $('#FormAltera').attr('action', '/recFinanDPSGerenciaRessarAlterarBase').submit();
      }
  })
}


// ========================================================================== 

function enviaAlert2() {
  swal({
      title: '',
      text: 'Confirma o cancelamento da Solicitação de Ressarcimento?',
      type: 'warning',                  //"warning", "error", "success", "info"
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#D80C1B',
      confirmButtonText: 'Sim',
      cancelButtonText: 'Não',
      closeOnClickOutside: false

    }, function(isConfirm) {

      if (isConfirm) {
        $('#FormAltera').attr('action', '/recFinanDPSGerenciaRessarCancelarBase').submit();
      }
  })
}

// ========================================================================== 

function enviaAlert3() {
  swal({
      title: '',
      text: 'Confirma que a solicitação de ressarcimento foi enviada para DAF?',
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
        $('#FormAltera').attr('action', '/recFinanDPSGerenciaRessarAutorizarBase').submit();
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

// ========================================================================== 

function validaCNPJ($cnpj) {
  // Verifica se um número foi informado
  if (!$cnpj.match(/[0-9]/)) {
      return 1;
  }
  
  // Elimina possivel mascara
  $cnpj = "00000000000000" + $cnpj.replace(/\D/g,'');
  $cnpj = $cnpj.slice(-14,-1) + $cnpj.slice(-1);
  
  // Verifica se o numero de digitos informados é igual a 14 
  if ($cnpj.length != 14) {        
    return 2;
  } 
                
  if ($cnpj == '00000000000000' || 
     $cnpj == '11111111111111' || 
     $cnpj == '22222222222222' || 
     $cnpj == '33333333333333' || 
     $cnpj == '44444444444444' || 
     $cnpj == '55555555555555' || 
     $cnpj == '66666666666666' || 
     $cnpj == '77777777777777' || 
     $cnpj == '88888888888888' || 
     $cnpj == '99999999999999') {
      return 3;
   } 

   $primeiro_digito_cnpj = $cnpj.substr(12, 1);    
   $segundo_digito_cnpj = $cnpj.substr(13, 1);    
  
   $j = 5;
   $k = 6;
   $soma1 = 0;
   $soma2 = 0;

  // Cálculo do primeiro Dígito
   for ($i = 0; $i < 12; $i++) {
      $j = $j == 1 ? 9 : $j;

      $soma1 += ($cnpj[$i] * $j);

      $j--;
   }

   $digito1 = $soma1 % 11 < 2 ? 0 : 11 - $soma1 % 11;

   // Cálculo do segundo Dígito
   for ($i = 0; $i < 13; $i++) {

     $k = $k == 1 ? 9 : $k;

     if ($i == 12) {
        $soma2 += ($digito1 * $k);      
     
     } else {
        $soma2 += ($cnpj[$i] * $k); 
     }

     $k--;
   }
   
   $digito2 = $soma2 % 11 < 2 ? 0 : 11 - $soma2 % 11;

   if ($digito1 != $primeiro_digito_cnpj || $digito2 != $segundo_digito_cnpj) {
      return 4
   }

   return 0;
}
