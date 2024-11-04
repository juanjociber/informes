var EquId = 0;
var Nombre = '';
var FechaInicial = '';
var FechaFinal = '';
var PaginasTotal = 0;
var PaginaActual = 0;

const vgLoader = document.querySelector('.container-loader-full');
window.onload = function() {
  document.getElementById('MenuInformes').classList.add('menu-activo','fw-bold');
  vgLoader.classList.add('loader-full-hidden');
};

$(document).ready(function() {
  $('#cbEquipo').select2({
    width: 'resolve', //Personalizar el alto del select, aplicar estilo.
    ajax: {
        delay: 450, //Tiempo de demora para buscar
        url: '/gesman/search/ListarEquipos.php',
        type: 'POST',
        dataType: 'json',
        data: function (params) {
          return {
              codigo: params.term // parametros a enviar al server. params.term captura lo que se escribe en el input
          };
        },
        processResults: function (datos) {
          return {
            results:datos.data.map(function(elem) {
                return {
                    id:elem.id,
                    text:elem.codigo,
                };
            })
          }
        },
        cache: true
    },
    placeholder: 'Seleccionar',
    allowClear: true, // Permite borrar la selección
    minimumInputLength:1 //Caracteres minimos para buscar
  });
});

$(document).ready(function() {
  $('#cbEquipo2').select2({
    dropdownParent: $('#modalAgregarInforme'),
    width: 'resolve', //Personalizar el alto del select, aplicar estilo.
    ajax: {
      delay: 450, //Tiempo de demora para buscar
      url: '/gesman/search/ListarEquipos.php',
      type: 'POST',
      dataType: 'json',
      data: function (params) {
          return {
              codigo: params.term // parametros a enviar al server. params.term captura lo que se escribe en el input
          };
      },
      processResults: function (datos) {
        return {
          results:datos.data.map(function(elem) {
              return {
                id: elem.id,
                text: elem.codigo,
              };
          })
        }
      },
      cache: true
    },
    placeholder: 'Seleccionar',
    minimumInputLength:1 //Caracteres minimos para buscar
  });
});

function FnModalAgregarInforme(){
  const modalAgregarOrden=new bootstrap.Modal(document.getElementById('modalAgregarInforme'), {
      keyboard: false
  }).show();
  return false;
}

async function FnBuscarInformes(){
    try {
      vgLoader.classList.remove('loader-full-hidden');
      Nombre = document.getElementById('txtNombre').value;
      EquId = document.getElementById('cbEquipo').value;
      FechaInicial = document.getElementById('dtpFechaInicial').value;
      FechaFinal = document.getElementById('dtpFechaFinal').value;
      PaginasTotal = 0
      PaginaActual = 0
      await FnBuscarInformes2();
    } catch (ex) {
        throw (ex.message);
        // showToast(ex.message, 'bg-danger');
    } finally {
        setTimeout(function () { vgLoader.classList.add('loader-full-hidden'); }, 500);
    }
}

async function FnBuscarInformes2(){
  try {
    const formData = new FormData();
    formData.append('nombre', Nombre);
    formData.append('equipo', EquId);
    formData.append('fechainicial', FechaInicial);
    formData.append('fechafinal', FechaFinal);
    formData.append('pagina', PaginasTotal);
    const response = await fetch('/informes/search/BuscarInformes.php', {
        method:'POST',
        body: formData
    });/*.then(response=>response.text()).then((response)=>{console.log(response)}).catch(err=>console.log(err));*/

    if (!response.ok) { 
      throw new Error(`${response.status} ${response.statusText}`);
    }
    const datos = await response.json();
    if (!datos.res) { 
      throw new Error(`${datos.msg}`); 
    }

    document.getElementById('tblInformes').innerHTML = '';
    let estado = '';
    datos.data.forEach(informe => {
      switch (informe.estado){
        case 1:
          estado='<span class="badge bg-danger">Anulado</span>';
        break;
        case 2:
          estado='<span class="badge bg-primary">Abierto</span>';
        break;
        case 3:
          estado='<span class="badge bg-success">Cerrado</span>';
        break;
        default:
          estado='<span class="badge bg-light text-dark">Unknown</span>';
      }
      document.getElementById('tblInformes').innerHTML +=`
      <div class="col-12">
          <div class="divselect border-bottom border-secondary mb-2 px-1" onclick="FnInforme(${informe.id}); return false;">
              <div class="div d-flex justify-content-between">
                  <p class="m-0"><span class="fw-bold">${informe.nombre}</span> <span class="text-secondary" style="font-size: 13px;">${informe.fecha}</span></p><p class="m-0">${estado}</p>
              </div>
              <div class="div">${informe.actividad}</div>
          </div>
      </div>`;
    });
    FnPaginacion(datos.pag);
  } catch (ex) {
    document.getElementById('tblInformes').innerHTML='';
    setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
    await Swal.fire({
        title: "Aviso",
        text: ex.message,
        icon: "info",
        timer: 2000
    });
    document.getElementById('tblInformes').innerHTML+=`
    <div class="col-12">
      <p class="fst-italic">Haga clic en el botón Buscar para obtener resultados.</p>
    </div>`;
  }
}

function FnPaginacion(cantidad) {
  try {
    PaginaActual += 1;
    if (cantidad == 15) {
      PaginasTotal += 15;
      document.getElementById("btnSiguiente").classList.remove('d-none');
    } else {
        document.getElementById("btnSiguiente").classList.add('d-none');
    }
    if (PaginaActual > 1) {
        document.getElementById("btnPrimero").classList.remove('d-none');
    } else {
        document.getElementById("btnPrimero").classList.add('d-none');
    }
  } catch (ex) {
    throw ex;
  }
}

async function FnBuscarSiguiente() {
  vgLoader.classList.remove('loader-full-hidden');
  try {
    await FnBuscarInformes2();
  } catch (ex) {
    document.getElementById("btnSiguiente").classList.add('d-none');
    showToast(ex.message, 'bg-danger');
  } finally {
    setTimeout(function () { vgLoader.classList.add('loader-full-hidden'); }, 500);
  }
}

async function FnBuscarPrimero() {
  vgLoader.classList.remove('loader-full-hidden');
  try {
    PaginasTotal = 0
    PaginaActual = 0
    await FnBuscarInformes2()
  } catch (ex) {
      document.getElementById("btnPrimero").classList.add('d-none');
      showToast(ex.message, 'bg-danger');
  } finally {
      setTimeout(function () { vgLoader.classList.add('loader-full-hidden'); }, 500);
  }
}

async function FnAgregarInforme() {
  try {
    vgLoader.classList.remove('loader-full-hidden');
    const formData = new FormData();
    formData.append('fecha', document.getElementById('txtFechaInforme').value);
    const actividad = document.getElementById('txtActividadInforme').value;
    formData.append('actividad', actividad);
    formData.append('supervisor', document.getElementById('cbSupervisorInforme').options[document.getElementById('cbSupervisorInforme').selectedIndex].text);
    formData.append('id', document.getElementById('cbEquipo2').value);
    formData.append('equ_codigo', document.getElementById('cbEquipo2').options[document.getElementById('cbEquipo2').selectedIndex].text);
    formData.append('equkm', document.getElementById('txtKm').value);
    formData.append('equhm', document.getElementById('txtHm').value);

    const response = await fetch("/informes/insert/AgregarInforme2.php", {
      method: "POST",
      body: formData
    });
    if (!response.ok) {
      throw new Error(`${response.status} ${response.statusText}`);
    }
    const datos = await response.json();
    if (!datos.res) {
      throw new Error(datos.msg);
    }
    setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
    await Swal.fire({
      title: "¡Éxito!",
      text: datos.msg,
      icon: "success",
      timer: 2000
    });
    setTimeout(() => {
      window.location.href = '/informes/EditarInforme.php?id=' + datos.id;
    }, 1000);
  } catch (error) {
      setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
      document.getElementById('msjAgregarInforme').innerHTML = `<div class="alert alert-danger mb-2 p-1 text-center" role="alert">${error.message}</div>`;
  }
}

function FnInforme(id){
  if(id > 0){
    window.location.href='/informes/Informe.php?id='+id;
  }
  return false;
}


