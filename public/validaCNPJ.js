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
