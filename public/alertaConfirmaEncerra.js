/* Javascript para Confirmação de Encerramento */

$("#btnEncerrar").click(function(){
  swal({
        title: '',
        text: 'Confirma o encerramento?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#D80C1B',
        confirmButtonText: 'Sim',
        cancelButtonText: 'Não',
        closeOnClickOutside: false

      }, function(isConfirm) {

      if (isConfirm) {
        $("#FormEncerra").submit();
      }
  })
}); 


