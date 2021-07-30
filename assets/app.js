/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/main.css';

// start the Stimulus application
import './bootstrap';


import $ from 'jquery';
window.jQuery =$;
window.$ = window.jQuery = $;
global.$ = global.jQuery = $;

import 'bootstrap/dist/css/bootstrap-grid.css';
import 'bootstrap/dist/css/bootstrap-reboot.css';
import 'bootstrap/dist/css/bootstrap-utilities.css';
import 'bootstrap/dist/css/bootstrap.css';



import 'datatables.net'
import 'datatables.net-dt'
import 'datatables.net-buttons-bs4'
import 'datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css'
import 'datatables.net-responsive-bs'

import './plugins/jquery-confirm-v3.3.4/dist/jquery-confirm.min'
import './plugins/jquery-confirm-v3.3.4/dist/jquery-confirm.min.css'
import './core'
import './dialogs'
import './tippy'
import ('toastr');
import('toastr/build/toastr.css');
window.toastr = toastr;
import './forms'

/* Configurando el idioma español para todas las tablas */
$.extend(true, $.fn.dataTable.defaults, {
    language: {
      sProcessing:
        '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><h3 style="display:inline;">Procesando...</h3>',
      sLengthMenu: "Mostrar _MENU_",
      sZeroRecords: "No se encontraron resultados",
      sEmptyTable: "Ningún dato disponible en esta tabla",
      sInfo: "Mostrando del _START_ al _END_ de un total de _TOTAL_ registros",
      sInfoEmpty: "Mostrando del 0 al 0 de un total de 0 registros",
      sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
      sInfoPostFix: "",
      sSearch: "Buscar:",
      sUrl: "",
      sInfoThousands: ",",
      sLoadingRecords: "Cargando...",
      oPaginate: {
        sFirst: "Primero",
        sLast: "Último",
        sNext: "Siguiente",
        sPrevious: "Anterior",
      },
      oAria: {
        sSortAscending: ": Activar para ordenar la columna de manera ascendente",
        sSortDescending:
          ": Activar para ordenar la columna de manera descendente",
      },
    },
  });
  function check(){
    $.ajax({
      url: '/annonimus',
      success:function(data){
        if(data.type === 'success'){
          if(data.online === false){
            location.reload();
          }
        }
      }
    })
  }

  $(document).ready(function(){

    setInterval(check,5000)
  })

   /* Notificando el error de una peticion por Ajax con Toastr */
   $(document).ajaxError(function (event, jqxhr, settings, thrownError) {
    if (jqxhr.statusText === 'abort' || jqxhr.statusText === 'canceled') {
        return;
    }
    console.log(jqxhr.statusText)
    
});