/* Javascript para Confirmação de Encerrar Vínculo - Desvincular */

$("#btnDesvincular").click(function(){
  swal({
        title: '',
        text: 'Confirma o encerramento do vínculo?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#D80C1B',
        confirmButtonText: 'Sim',
        cancelButtonText: 'Não',
        closeOnClickOutside: false

      }, function(isConfirm) {

      if (isConfirm) {
        $("#FormDesvincula").submit();
      }
  })
}); 



