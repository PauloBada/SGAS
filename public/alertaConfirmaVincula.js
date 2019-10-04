/* Javascript para Confirmação de Encerrar Vínculo - Desvincular */

  $("#btnBuscar").click(function(){
  swal({
        title: '',
        text: 'Confirma a vinculação?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#D80C1B',
        confirmButtonText: 'Sim',
        cancelButtonText: 'Não',
        closeOnClickOutside: false

      }, function(isConfirm) {

      if (isConfirm) {
        $("#FormVincula").submit();
      }
  })
}); 


