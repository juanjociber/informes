// INICIALIZANDO VARIABLES PARA MODAL GLOBAL
const vgLoader = document.querySelector('.container-loader-full');
let modalEditarActividad;

document.addEventListener('DOMContentLoaded', () => {
  modalEditarActividad = new bootstrap.Modal(document.getElementById('modalEditarActividad'), { keyboard: false });
  // document.getElementById('MenuInformes').classList.add('menu-activo','fw-bold');
  vgLoader.classList.add('loader-full-hidden');
});

// AGREGAR INFORME ACTIVIDAD
const FnAgregarInformeActividades = async () => {
  try {
    const formData = new FormData();
    formData.append('infid', document.getElementById('txtActividadInfid1').value);
    formData.append('actividad', document.getElementById('txtActividad1').value.trim());
    formData.append('diagnostico', document.getElementById('txtDiagnostico1').value.trim());
    formData.append('trabajos', document.getElementById('txtTrabajo1').value.trim());
    formData.append('observaciones', document.getElementById('txtObservacion1').value.trim());
    const response = await fetch('/informes/insert/AgregarInformeActividad.php', {
      method: 'POST',
      body: formData
    });
    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }
    const datos = await response.json();
    if (datos.res) {
        setTimeout(function(){
          vgLoader.classList.add('loader-full-hidden');
        }, 500);
      Swal.fire({
        title: '¡Éxito!',
        text: datos.msg,
        icon: 'success',
        timer: 2000
      });
      setTimeout(() => { location.reload(); }, 1000);
    } else {
      Swal.fire({
        title: 'Aviso',
        text: datos.msg,
        icon: 'info',
        timer: 2000
      });
    }
  } catch (error) {
    setTimeout(function(){
      vgLoader.classList.add('loader-full-hidden');
    }, 500);
    Swal.fire({
      title: 'Aviso',
      text: `${error.message}`,
      icon: 'error',
      timer:2000
    });
  }
};

// ABRIR MODAL INFORME SUB-ACTIVIDAD
const FnModalAgregarInformeActividades = async (id) => {
  const modal = new bootstrap.Modal(document.getElementById('modalNuevaSubActividad'), { keyboard: false });
  modal.show();
  document.getElementById('txtActividadOwnid').value = id;
};

// AGREGAR INFORME SUB-ACTIVIDAD
const FnAgregarInformeActividades2 = async () =>{
  const formData = new FormData();
  formData.append('infid', document.getElementById('txtActividadInfid2').value);
  formData.append('ownid', document.getElementById('txtActividadOwnid').value);
  formData.append('tipo','act');
  formData.append('actividad', document.getElementById('txtActividad2').value.trim());
  formData.append('diagnostico', document.getElementById('txtDiagnostico2').value.trim());
  formData.append('trabajos', document.getElementById('txtTrabajo2').value.trim());
  formData.append('observaciones', document.getElementById('txtObservacion2').value.trim());
  try {
    const response = await fetch('/informes/insert/AgregarInformeActividades.php', {
      method: 'POST',
      body: formData
    });
    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }
    const datos = await response.json();
    if (datos.res) {
      setTimeout(function(){
        vgLoader.classList.add('loader-full-hidden');
      }, 500);
      Swal.fire({
        title: '¡Éxito!',
        text: datos.msg,
        icon: 'success',
        timer: 2000
      });
      setTimeout(() => { location.reload(); }, 1000);
    } else {
      Swal.fire({
        title: 'Aviso',
        text: datos.msg,
        icon: 'info',
        timer: 2000
      });
    }
  } catch (error) {
    setTimeout(function(){
      vgLoader.classList.add('loader-full-hidden');
    }, 500);
    Swal.fire({
      title: 'Aviso',
      text: error.message,
      icon: 'error',
      timer: 2000
    });
  }
};

//BUSCAR INFORME ACTIVIDAD
const FnModalModificarInformeActividades = async (id) => {
  modalEditarActividad.show();
  const formData = new FormData();
  formData.append('id', id);
  try {
    const response = await fetch('/informes/search/BuscarInformeActividad.php', {
        method: 'POST',
        body: formData
    });
    if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
    }
    const datos = await response.json();
    if (datos.res) { 
      setTimeout(function(){
        vgLoader.classList.add('loader-full-hidden');
      }, 500);
      document.getElementById('txtActividadId').value = datos.data.id;
      document.getElementById('txtactividad3').value = datos.data.actividad;
      document.getElementById('txtDiagnostico3').value = datos.data.diagnostico;
      document.getElementById('txtTrabajo3').value = datos.data.trabajos;
      document.getElementById('txtObservacion3').value = datos.data.observaciones;  
    } else {
      Swal.fire({
        title: 'Aviso',
        text: datos.msg,
        icon: 'info',
        timer: 2000
      });
    }
  } catch (error) {
    setTimeout(function(){
      vgLoader.classList.add('loader-full-hidden');
    }, 500);
    Swal.fire({
      title: 'Aviso',
      text: error.message,
      icon: 'error',
      timer:2000
    });
  }
};

//MODIFICAR INFORME ACTIVIDAD
const FnModificarInformeActividades = async () => {
  try {
    vgLoader.classList.remove('loader-full-hidden');
    const formData = new FormData();
    formData.append('id', document.getElementById('txtActividadId').value);
    formData.append('actividad', document.getElementById('txtactividad3').value);
    formData.append('diagnostico', document.getElementById('txtDiagnostico3').value);
    formData.append('trabajos', document.getElementById('txtTrabajo3').value);
    formData.append('observaciones', document.getElementById('txtObservacion3').value);

    const response = await fetch('/informes/update/ModificarInformeActividades.php', {
      method: 'POST',
      body: formData
    });
    if (!response.ok) {throw new Error(`${response.status} ${response.statusText}`);}

    const datos = await response.json();
    if (!datos.res) {
      throw new Error(datos.msg);
    }
    setTimeout(function() {
      location.reload();
    }, 500)
    setTimeout(function(){vgLoader.classList.add('loader-full-hidden');}, 500);
    Swal.fire({
      title:'¡Éxito!', 
      text:datos.msg, 
      icon:'success', 
      timer:2000
    });
  } catch (error) {
    Swal.fire({
      title: 'Aviso', 
      text: error.message, 
      icon: 'error', 
      timer:1000
    });
    setTimeout(function(){vgLoader.classList.add('loader-full-hidden');}, 500);
  }
};

// FUNCIÓN ELIMINAR ACTIVIDAD
const FnEliminarInformeActividades = async (id) => {
  const formData = new FormData();
  formData.append('id', id);
  try {
    const response = await fetch('/informes/delete/EliminarInformeActividades.php', {
      method: 'POST',
      body: formData
    });

    if (!response.ok) {
      throw new Error(`Error en la respuesta del servidor: ${response.statusText}`);
    }
    const datos = await response.json();
    if (datos.res) {
      Swal.fire({
        title: "¡Éxito!",
        text: datos.msg,
        icon: "success"
      });
      setTimeout(function() {location.reload();}, 500);
    } else {
      Swal.fire({
        title: "Aviso",
        text: datos.msg,
        icon: "info",
        timer: 1000
      });
    }
  } catch (error) {
    Swal.fire({
      title: "Aviso",
      text: error.message,
      icon: "error",
      timer: 1000
    });
  }
};

// ABRIR MODAL REGISTRAR IMAGEN
const FnModalAgregarArchivo = (id) => {
  var modal = new bootstrap.Modal(document.getElementById('modalAgregarImagen'), { keyboard: false });
  modal.show();
  document.getElementById('txtActividadOwnid').value = id;
};

/*================================
  FUNCIONES PARA CARGA DE IMÁGENES
  ================================*/
function FnRedimensionImagen(fileInputId, divId) {
  const MAX_WIDTH = 1080;
  const MAX_HEIGHT = 720;
  const MIME_TYPE = "image/jpeg";
  const QUALITY = 0.7;

  const $divImagen = document.getElementById(divId);

  document.getElementById(fileInputId).addEventListener('change', function (event) {
    const file = event.target.files[0];

    if (!isValidFileType(file)) {
      alert('Tipo de archivo no permitido.');
      return;
    }

    if (!isValidFileSize(file)) {
      alert('El tamaño del archivo excede los 3MB.');
      return;
    }

    while ($divImagen.firstChild) {
      $divImagen.removeChild($divImagen.firstChild);
    }

    if (file.type.startsWith('image/')) {
      displayImage(file, $divImagen, MAX_WIDTH, MAX_HEIGHT, MIME_TYPE, QUALITY);
    }
  });
}

function displayImage(file, $divImagen, maxWidth, maxHeight, mimeType, quality) {
  const reader = new FileReader();
  reader.onload = function (event) {
    const imageUrl = event.target.result;
    const canvas = document.createElement('canvas');
    canvas.style.border = '1px solid black';

    $divImagen.appendChild(canvas);
    const context = canvas.getContext('2d');

    const image = new Image();
    image.onload = function () {
      const [newWidth, newHeight] = calculateSize(image, maxWidth, maxHeight);
      canvas.width = newWidth;
      canvas.height = newHeight;
      canvas.id = "canvas";
      context.drawImage(image, 0, 0, newWidth, newHeight);
      // Agregar marca de agua
      context.strokeStyle = 'rgba(216, 216, 216, 0.7)';
      context.font = '15px Verdana';
      context.strokeText("GPEM SAC", 10, newHeight - 10);

      canvas.toBlob(
        (blob) => {
          displayInfo('Original: ', file, $divImagen);
          displayInfo('Comprimido: ', blob, $divImagen);
        },
        mimeType,
        quality
      );
    };
    image.src = imageUrl;
  };
  reader.readAsDataURL(file);
}

function displayInfo(label, file, $divImagen) {
  const p = document.createElement('p');
  p.classList.add('text-secondary', 'm-0', 'fs-6');
  p.innerText = `${label} ${readableBytes(file.size)}`;
  $divImagen.append(p);
}

function isValidFileType(file) {
  const acceptedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
  return acceptedTypes.includes(file.type);
}

function isValidFileSize(file) {
  const maxSize = 3 * 1024 * 1024; // 3MB
  return file.size <= maxSize;
}

function calculateSize(img, maxWidth, maxHeight) {
  let width = img.width;
  let height = img.height;
  if (width > height) {
    if (width > maxWidth) {
      height = Math.round((height * maxWidth) / width);
      width = maxWidth;
    }
  } else {
    if (height > maxHeight) {
      width = Math.round((width * maxHeight) / height);
      height = maxHeight;
    }
  }
  return [width, height];
}

function readableBytes(bytes) {
  const i = Math.floor(Math.log(bytes) / Math.log(1024)),
    sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
  return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
}

FnRedimensionImagen('fileImagen', 'divImagen');
FnRedimensionImagen('fileImagen2', 'divImagen2');


async function FnAgregarArchivo(){
  try {
    vgLoader.classList.remove('loader-full-hidden');
    // var archivo;
    // if(document.getElementById('canvas')){
    //   archivo = document.querySelector("#canvas").toDataURL("image/jpeg");
    // }else if(document.getElementById('fileImagen').files.length == 1){
    //   archivo = fileOrCanvasData = document.getElementById('fileImagen').files[0];
    // }else{
    //   throw new Error('No se reconoce el archivo');
    // }
    archivo = document.getElementById('fileImagen').files[0];

    console.log(archivo);
    const formData = new FormData();
    formData.append('refid', document.getElementById('txtActividadOwnid').value);
    formData.append('titulo', document.getElementById('txtTitulo').value);
    formData.append('descripcion', document.getElementById('txtDescripcion').value);
    formData.append('archivo', archivo);
    formData.append('tabla', 'INFD');

    const response = await fetch('/gesman/insert/AgregarArchivo.php', {
      method:'POST',
      body: formData
    });
    if(!response.ok){throw new Error(`${response.status} ${response.statusText}`);}
    const datos = await response.json();
    if(datos.res){
      setTimeout(() => { 
        vgLoader.classList.add('loader-full-hidden'); 
      }, 500);
      Swal.fire({ 
        title: '¡Éxito!', 
        text: datos.msg, 
        icon: 'success', 
        // timer:2000 
      });
      // setTimeout(() => { location.reload(); }, 1000);
    }else {
      Swal.fire({
        title: 'Aviso',
        text: datos.msg,
        icon: 'info',
        // timer: 2000
      });
    }
  } catch (error) {
    setTimeout(function() {
      vgLoader.classList.add('loader-full-hidden');
    }, 500);
    Swal.fire({
      title: 'Aviso',
      text: error.message,
      icon: 'error',
      // timer:2000
    });
  }
}
const FnModalModificarArchivo = async (id,refid)=>{
  document.getElementById('txtArchivoId').value = id;
  document.getElementById('txtArchivoRefId').value = refid;
  const formData = new FormData();
  formData.append('id', id);
  formData.append('refid', refid);
  try {
    const response = await fetch('/gesman/search/BuscarArchivo.php', {
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
    document.getElementById('txtTitulo2').value = datos.data[0].titulo;
    document.getElementById('txtDescripcion2').value = datos.data[0].descripcion;
    document.getElementById('divImagen2').innerHTML = datos.data[0].nombre;
  } catch (error) {
    Swal.fire({
      title: 'Aviso',
      text: error.message,
      icon: 'error',
      timer:2000
    });
  }
  const modalModificarArchivoTituloDescripcion = new bootstrap.Modal(document.getElementById('modalModificarArchivoTituloDescripcion'), {keyboard: false}).show();
  return false;
}

const FnModificarArchivo = async () => {
  try {
    vgLoader.classList.remove('loader-full-hidden');
    const formData = new FormData();
    formData.append('id', document.getElementById('txtArchivoId').value);
    formData.append('titulo', document.getElementById('txtTitulo2').value);
    formData.append('descripcion', document.getElementById('txtDescripcion2').value);
    formData.append('tipo','INFD')
    const fileInput = document.getElementById('fileImagen2');
    if (fileInput.files.length === 1) {
      formData.append('archivo', fileInput.files[0]); 
    }
    const response = await fetch('/gesman/update/ModificarActividadArchivo.php', {
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
    Swal.fire({ 
      title: "¡Éxito!", 
      text: datos.msg, 
      icon: "success", 
      timer:2000 
    });
    setTimeout(() => { 
      location.reload(); 
    }, 1000);
  } catch (error) {
    setTimeout(() => { 
      vgLoader.classList.add('loader-full-hidden'); 
    }, 500);
    Swal.fire({ 
      title: 'Aviso', 
      text: error.message, 
      icon: 'error', 
      timer: 2000 
    });
  }
}

//ELIMINAR ARCHIVO
const FnEliminarArchivo = async (id,refid) => {
  try {
    vgLoader.classList.remove('loader-full-hidden');
    const formData = new FormData();
    formData.append('id', id);
    formData.append('refid', refid);
    const response = await fetch('/gesman/delete/EliminarArchivo.php', {
      method: 'POST',
      body: formData,
      headers: {
        'Accept': 'application/json'
      }
    });
    if (!response.ok) {
      throw new Error(`Error: ${response.status} ${response.statusText}`);
    }
    const datos = await response.json();
    if (datos.res) {
      setTimeout(() => { 
        vgLoader.classList.add('loader-full-hidden'); 
      }, 500);
      Swal.fire({
        title: "¡Éxito!",
        text: datos.msg,
        icon: "success",
        timer: 2000
      });
      setTimeout(() => { 
        location.reload(); 
      }, 1000);
    }else {
      await Swal.fire({
        title: "Aviso",
        text: datos.msg,
        icon: "info",
        timer: 2000
      });
    }
  } catch (error) {
    setTimeout(function() {
      vgLoader.classList.add('loader-full-hidden');
    }, 500);
    Swal.fire({
      title: "Aviso",
      text: error.message,
      icon: "error",
      timer:1000
    });
  }
};

// MOSTRAR TÍTULOS
function mostrarTitulo(clase) {
  const titulos = document.querySelectorAll(`.${clase}`)
  titulos.forEach((titulo) => {
    const tituloElemento = titulo.previousElementSibling;
    
    if (titulo.textContent.trim() === '') {
      // OCULTA TITULO SI ESTÁ VACIO CONTENIDO
      tituloElemento.style.display = 'none';
    } else {
      // MUESTRA TÍTULO SI EXISTE CONTENIDO
      tituloElemento.style.display = 'flex';
    }
  });
}
mostrarTitulo('diagnostico');
mostrarTitulo('trabajo');
mostrarTitulo('observacion');

function editarTitulo(id) {
  const titulo = document.getElementById(`titulo-${id}`).innerText;
}

function editarDescripcion(id) {
  const descripcion = document.getElementById(`descripcion-${id}`).innerText;
}

function editarImagen(id) {
  const nuevaImagen = prompt("Introduce la URL de la nueva imagen:");
  if (nuevaImagen) {
    document.getElementById(`imagen-${id}`).src = nuevaImagen;
  }
}

function FnResumenInforme(){
  id = document.getElementById('txtInformeId').value;
  if(id > 0){
      window.location.href='/informes/Informe.php?id='+id;
  }
  return false;
}

function FnListarInformes(){
  window.location.href='/informes/Informes.php';
  return false;
};

/** EVENTO CAMBIO DE POSICIÓN */
const lista = document.getElementById('accordion-container');
// const archivo = document.getElementById('archivo');

let sortableActividades;
// let sortableArchivos;

function FnInitSorteable() {
  if (!lista) {
    return; 
  }
  const mediaQuery = window.matchMedia('(min-width: 768px)');

  if (mediaQuery.matches) {
    if (!sortableActividades) {
      sortableActividades = Sortable.create(lista, {
        animation: 150,
        onEnd: () => {
          const orden = lista.children; 
          const nuevaOrden = Array.from(orden).map(item => item.id);
          // ENVIAR NUEVOS 'id' AL SERVIDOR
          fetch('/informes/update/ModificarActividades.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({ orden: nuevaOrden })
          })
          .then(response => response.json())
          .then(data => {
            setTimeout(() => {
              vgLoader.classList.add('loader-full-hidden');
            }, 500);
            setTimeout(() => { location.reload(); }, 1000);
          })
          .catch(error => {
            Swal.fire({
              title: "Aviso",
              text: error.message,
              icon: "error",
              timer: 2000
            });
          });
        },
        group: "lista-actividades",
        store: {
          // GUARDAR ORDEN DE ACTIVIDADES
          set: (sortable) => {
            const orden = sortable.toArray();
            localStorage.setItem(sortable.options.group.name, orden.join('|'));
          },
          // OBTENER ORDEN DE LISTA 
          get: (sortable) => {
            const orden = localStorage.getItem(sortable.options.group.name);
            return orden ? orden.split('|') : [];
          }
        }
      });
    }
  } else {
    if (sortableActividades) {
      sortableActividades.destroy();
      sortableActividades = null;
    }
  }
}

// Inicializar las funciones
FnInitSorteable();
// FnInitSorteableArchivo();

// Escuchar cambios en el tamaño de la ventana
const mediaQuery = window.matchMedia('(min-width: 768px)');
mediaQuery.addEventListener('change', (e) => {
  FnInitSorteable();
  // FnInitSorteableArchivo();
});








