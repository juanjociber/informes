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
      setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
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
      const errorText = await response.text();
      setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
      await Swal.fire({
        icon: 'error',
        title: 'Error',
        text: errorText,
        timer: 2000
      });
      return;
    }
    const datos = await response.json();
    setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
    if (!datos.res) {
      await Swal.fire({
        icon: 'error',
        title: 'Error',
        text: datos.msg,
        timer: 2000
      });
      return;
    }
    await Swal.fire({
      icon: 'success',
      title: 'Éxito',
      text: datos.msg,
      timer: 2000
    });
    document.querySelector('#actividadId').textContent = actividad;
    setTimeout(() => { location.reload(); }, 100);
  } catch (error) {
    setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
    await Swal.fire({
      icon: 'error',
      title: 'Error',
      text: `${error.message}`,
      timer: 1000,
      showConfirmButton: false
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
  const modal = new bootstrap.Modal(document.getElementById('agregarActividadModal'), { keyboard: false });
  modal.show();
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
}

const FnAgregarDetalleInformeActividad = async () => {
  try {
    vgLoader.classList.remove('loader-full-hidden');
    const formData = new FormData();
    formData.append('infid', document.getElementById('txtIdInforme').value);
    formData.append('actividad', document.getElementById('registroActividadInput').value.trim());
    formData.append('tipo', tipoSeleccionado);

    const response = await fetch('/informes/insert/AgregarDetalleInformeActividad.php', {
      method: 'POST',
      body: formData
    });
    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }
    const result = await response.json();
    setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
    if (result.res) {
      await Swal.fire({
        title: 'Éxito',
        text: result.msg,
        icon: 'success',
        timer: 2000
      });
      setTimeout(() => { location.reload(); }, 100);
    } else {
      await Swal.fire({
        title: 'Error',
        text: result.msg,
        icon: 'error',
        confirmButtonText: 'OK',
        timer: 2000
      });
    }
  } catch (error) {
    setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
    await Swal.fire({
      title: 'Error',
      text: `${error.message}`,
      icon: 'error',
      confirmButtonText: 'OK',
      timer: 2000
    });
  }
};

// FUNCIÓN ABRIR MODAL Y BUSCA DATA ENVIADA POR EL SERVIDOR
const FnModalModificarDetalleInforme = async (id, cabecera) => {
  const modal = new bootstrap.Modal(document.getElementById('modalGeneral'), {keyboard: false});
  modal.show();
  document.getElementById('txtIdtblDetalleInf').value = id;
    
  const formData = new FormData();
  formData.append('id', id);
  try {
    const response = await fetch('/informes/search/BuscarDetalleInformeActividad.php', {
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
      icon: 'error',
      title: 'Error',
      text: error,
      timer: 1000
    });
  }
}

// FUNCIÓN MODIFICAR ACTIVIDAD DETALLE
const FnModificarDetalleInformeActividad = async () => {
  try {
    vgLoader.classList.remove('loader-full-hidden');
    const formData = new FormData();
    formData.append('id', document.getElementById('txtIdtblDetalleInf').value);
    formData.append('actividad', document.getElementById('actividadModalInput').value.trim());

    const response = await fetch('/informes/update/ModificarDetalleInformeActividad.php', {
      method: 'POST',
      body: formData
    });
    if (!response.ok) {
      const errorText = await response.text();
      setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
      await Swal.fire({
        icon: 'error',
        title: 'Error',
        text: errorText,
        timer: 2000
      });
      return;
    }
    const datos = await response.json();
    setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
    if (!datos.res) {
      await Swal.fire({
        icon: 'error',
        title: 'Error',
        text: datos.msg,
        timer: 2000
      });
      return;
    }
    await Swal.fire({
      icon: 'success',
      title: 'Éxito',
      text: datos.msg,
      timer: 2000
    });
    setTimeout(() => { location.reload(); }, 100);
  } catch (error) {
    setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
    await Swal.fire({
      icon: 'error',
      title: 'Error',
      text: `${error.message}`,
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
    const response = await fetch('/informes/delete/EliminarDetalleInformeActividad.php', {
      method: 'POST',
      body: formData
    });
    if (!response.ok) {
      throw new Error(`Error en la respuesta del servidor: ${response.statusText}`);
    }
    const result = await response.json();
    setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
    if (result.res) {
      await Swal.fire({
        title: "Éxito",
        text: result.msg,
        icon: "success",
        timer: 2000,
      });
    } else {
      await Swal.fire({
        title: "Error",
        text: result.msg,
        icon: "error",
        timer: 2000,
      });
    }
    setTimeout(() => { location.reload(); }, 100);
  } catch (error) {
    setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
    await Swal.fire({
      title: "Error",
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