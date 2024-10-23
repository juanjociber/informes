// INICIALIZANDO VARIABLES PARA MODAL GLOBAL
let modalEditarActividad;
const vgLoader = document.querySelector('.container-loader-full');

document.addEventListener('DOMContentLoaded', () => {
  const modalElement = document.getElementById('modalActividad');
  if (modalElement) {
    modalEditarActividad = new bootstrap.Modal(modalElement, { keyboard: false });
  }
  document.getElementById('MenuInformes').classList.add('menu-activo','fw-bold');
  vgLoader.classList.add('loader-full-hidden');
});

const FnModalModificarInformeActividad = async (id) => {
  modalEditarActividad.show();
};

//MODIFICAR ACTIVIDAD
const FnModificarInformeActividad = async () => {
  try {
    vgLoader.classList.remove('loader-full-hidden');
    const id = document.getElementById('txtIdInforme').value;
    const actividad = document.getElementById('modalActividadInput').value;
    if (isNaN(id)) {
      setTimeout(() => { location.reload(); }, 500);
      return;
    }
    const formData = new FormData();
    formData.append('id', id);
    formData.append('actividad', actividad);
    const response = await fetch('/informes/update/ModificarInformeActividad.php', {
      method: 'POST',
      body: formData
    });
    if (!response.ok) {
      throw new Error(`${response.status} ${response.statusText}`);
    }
    const datos = await response.json();
    if (datos.res) {
      setTimeout(() => { 
        vgLoader.classList.add('loader-full-hidden'); 
      }, 500);
      await Swal.fire({
        title: "¡Éxito!",
        text: datos.msg,
        icon: "success",
        timer: 2000
      });
      setTimeout(() => { location.reload(); }, 1000);
    }else {
      await Swal.fire({
        title: "Aviso",
        text: datos.msg,
        icon: "info",
        timer: 2000
      });
    }
  } catch (error) {
    setTimeout(() => { 
      vgLoader.classList.add('loader-full-hidden'); 
    }, 500);
    await Swal.fire({
      title: 'Aviso',
      text: error.message,
      icon: 'error',
      timer: 2000
    });
  }
};

/**------------------------------------------------------------
 * FUNCIONES : ANTECEDENTES - CONCLUSIONES - RECOMENDACIONES
 * ------------------------------------------------------------*/
let tipoSeleccionado = '';
// FUNCIÓN AGREGAR
const FnModalAgregarDetalleInforme = async (tipo) => {
  tipoSeleccionado = tipo;
  // ACTUALIZAR EL TEXTO DEL H5 SEGÚN EL TIPO
  const modalTitle = document.getElementById('cabeceraRegistrarModal');
  switch(tipo) {
    case 'ant':
      modalTitle.textContent = 'Agregar Antecedente';
      break;
    case 'ana':
      modalTitle.textContent = 'Agregar Análisis';
      break;
    case 'con':
      modalTitle.textContent = 'Agregar Conclusión';
      break;
    case 'rec':
      modalTitle.textContent = 'Agregar Recomendación';
      break;
    default:
      modalTitle.textContent = 'Agregar';
  }
  const agregarActividadModal = new bootstrap.Modal(document.getElementById('agregarActividadModal'), { keyboard: false }).show();
  return false;
}

const FnAgregarInformeActividades = async () => {
  try {
    vgLoader.classList.remove('loader-full-hidden');
    const formData = new FormData();
    formData.append('infid', document.getElementById('txtIdInforme').value);
    formData.append('actividad', document.getElementById('registroActividadInput').value.trim());
    formData.append('tipo', tipoSeleccionado);
    
    const response = await fetch('/informes/insert/AgregarInformeActividades.php', {
      method: 'POST',
      body: formData
    });
    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }
    const datos = await response.json();
    if (!datos.res) {
      throw new Error(datos.msg);
    }
    setTimeout(() => { 
      vgLoader.classList.add('loader-full-hidden'); 
    }, 500);
    await Swal.fire({
      title: "¡Éxito!",
      text: datos.msg,
      icon: "success",
      timer: 2000
    });
    setTimeout(() => { location.reload(); }, 500);
  } catch (error) {
    setTimeout(() => { 
      vgLoader.classList.add('loader-full-hidden'); 
    }, 500);
    await Swal.fire({
      title: 'Aviso',
      text: error.message,
      icon: 'error',
      timer: 2000
    });
  }
};

// FUNCIÓN ABRIR MODAL Y BUSCA DATA ENVIADA POR EL SERVIDOR
const FnModalModificarActividad = async (id, cabecera) => {
  const modal = new bootstrap.Modal(document.getElementById('modalGeneral'), {keyboard: false});
  modal.show();
  document.getElementById('txtIdtblDetalleInf').value = id;
    
  const formData = new FormData();
  formData.append('id', id);
  try {
    const response = await fetch('/informes/search/BuscarInformeActividad.php', {
      method: 'POST',
      body: formData
    });
    if (!response.ok) { 
      throw new Error(response.status + ' ' + response.statusText); 
    }
    const datos = await response.json();
    if (!datos.res) { 
      throw new Error(datos.msg); 
    }
    //console.log(datos);
    // MOSTRANDO DATA RECIBIDA DE SERVIDOR
    document.getElementById('actividadModalInput').value = datos.data.actividad;
    document.getElementById('diagnosticoModalInput').value = datos.data.diagnostico;
    document.getElementById('trabajoModalInput').value = datos.data.trabajos;
    document.getElementById('observacionModalInput').value = datos.data.observaciones;

    // ACTUALIZAR EL TEXTO DEL H5 SEGÚN EL TIPO
    const modalTitle = document.getElementById('cabeceraModal');
    switch(cabecera) {
      case 'antecedente':
        modalTitle.textContent = 'Modificar Antecedente';
        break;
      case 'analisis':
        modalTitle.textContent = 'Modificar Análisis';
        break;
      case 'conclusion':
        modalTitle.textContent = 'Modificar Conclusión';
        break;
      case 'recomendacion':
        modalTitle.textContent = 'Modificar Recomendación';
        break;
      default:
        modalTitle.textContent = 'Modificar';
    }
  } 
  catch (error) {
    Swal.fire({
      icon: 'Aviso',
      title: 'Error',
      text: error,
      timer: 2000
    });
  }
}

// FUNCIÓN MODIFICAR ACTIVIDAD DETALLE
const FnModificarInformeActividades = async () => {
  try {
    vgLoader.classList.remove('loader-full-hidden');
    const formData = new FormData();
    formData.append('id', document.getElementById('txtIdtblDetalleInf').value);
    formData.append('actividad', document.getElementById('actividadModalInput').value.trim());
    const response = await fetch('/informes/update/ModificarInformeActividades.php', {
      method: 'POST',
      body: formData
    });
    if (!response.ok) {
      throw new Error(response.status + ' ' + response.statusText); 
    }
    const datos = await response.json();
    setTimeout(() => { 
      vgLoader.classList.add('loader-full-hidden'); 
    }, 500);
    await Swal.fire({
      title: "¡Éxito!",
      text: datos.msg,
      icon: "success",
      timer: 2000
    });
    setTimeout(() => { location.reload(); }, 500);
  } catch (error) {
    setTimeout(() => { 
      vgLoader.classList.add('loader-full-hidden'); 
    }, 500);
    await Swal.fire({
      title: 'Aviso',
      icon: 'error',
      text: error.message,
      timer: 2000
    });
  }
};

// FUNCIÓN ELIMINAR
const FnModalEliminarDetalleInformeActividad = async (id) => {
  try {
    vgLoader.classList.remove('loader-full-hidden');
    const formData = new FormData();
    formData.append('id', id);
    const response = await fetch('/informes/delete/EliminarInformeActividades.php', {
      method: 'POST',
      body: formData
    });
    if (!response.ok) {
      throw new Error(`${response.status} ${response.statusText}`);
    }
    const datos = await response.json();
    if (!datos.res) {
      throw new Error(datos.msg);
    }
    setTimeout(() => { 
      vgLoader.classList.add('loader-full-hidden'); 
    }, 500);
    await Swal.fire({
      title: "¡Éxito!",
      text: datos.msg,
      icon: "success",
      timer: 2000,
    });
    setTimeout(() => { 
      location.reload(); 
    }, 500);
  } catch (error) {
    setTimeout(() => { 
      vgLoader.classList.add('loader-full-hidden'); 
    }, 500);
    await Swal.fire({
      title: "Aviso",
      text: error.message,
      icon: "error",
      timer: 1000
    });
  }
};

function FnResumenInforme(){
  id = document.getElementById('txtIdInforme').value;
  if(id > 0){
    window.location.href='/informes/Informe.php?id='+id;
  }
  return false;
}

function FnListarInformes(){
  window.location.href='/informes/Informes.php';
  return false;
}