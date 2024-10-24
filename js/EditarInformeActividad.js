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
  const formData = new FormData();
  formData.append('infid', document.getElementById('txtActividadInfid1').value);
  formData.append('actividad', document.getElementById('txtActividad1').value.trim());
  formData.append('diagnostico', document.getElementById('txtDiagnostico1').value.trim());
  formData.append('trabajos', document.getElementById('txtTrabajo1').value.trim());
  formData.append('observaciones', document.getElementById('txtObservacion1').value.trim());
  try {
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
        text: result.msg,
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
  formData.append('actividad', document.getElementById('txtActividad2').value.trim());
  formData.append('diagnostico', document.getElementById('txtDiagnostico2').value.trim());
  formData.append('trabajos', document.getElementById('txtTrabajo2').value.trim());
  formData.append('observaciones', document.getElementById('txtObservacion2').value.trim());
  try {
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

/**================================
 FUNCIONES PARA CARGA DE IMÁGENES
===================================* 
*/
const MAX_WIDTH = 1080;
const MAX_HEIGHT = 720;
const MIME_TYPE = "image/jpeg";
const QUALITY = 0.7;

const $divImagen = document.getElementById("divImagen");

document.getElementById('fileImagen').addEventListener('change', function(event) {
  vgLoader.classList.remove('loader-full-hidden');
  
  const file = event.target.files[0];

  if (!isValidFileType(file)) {
      console.log('El archivo', file.name, 'Tipo de archivo no permitido.');
  }

  if (!isValidFileSize(file)) {
      console.log('El archivo', file.name, 'El tamaño del archivo excede los 3MB.');
  }

  while ($divImagen.firstChild) {
      $divImagen.removeChild($divImagen.firstChild);
  }

  if (file.type.startsWith('image/')) {
      displayImage(file);
  }

  console.log('Nombre del archivo:', file.name);
  console.log('Tipo del archivo:', file.type);
  console.log('Tamaño del archivo:', file.size, 'bytes');

  setTimeout(function() {
    vgLoader.classList.add('loader-full-hidden');
  }, 1000)
});

function isValidFileType(file) {
  const acceptedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
  return acceptedTypes.includes(file.type);
}

function isValidFileSize(file) {
  const maxSize = 3 * 1024 * 1024; // 4MB en bytes
  return file.size <= maxSize;
}

function displayImage(file) {
  const reader = new FileReader();
  reader.onload = function(event) {
    const imageUrl = event.target.result;
    const canvas = document.createElement('canvas');
    canvas.style.border = '1px solid black';

    $divImagen.appendChild(canvas);
    const context = canvas.getContext('2d');

    const image = new Image();
    image.onload = function() {
      const [newWidth, newHeight] = calculateSize(image, MAX_WIDTH, MAX_HEIGHT);
      canvas.width = newWidth;
      canvas.height = newHeight;
      canvas.id="canvas";
      context.drawImage(image, 0, 0, newWidth, newHeight);

      // Agregar texto como marca de agua
      context.strokeStyle = 'rgba(216, 216, 216, 0.7)';// color del texto (blanco con opacidad)
      context.font = '15px Verdana'; // fuente y tamaño del texto
      context.strokeText("GPEM SAC", 10, newHeight-10);// texto y posición

      canvas.toBlob(
        (blob) => {
          // Handle the compressed image. es. upload or save in local state
          displayInfo('Original: ', file);
          displayInfo('Comprimido: ', blob);
        },
        MIME_TYPE,
        QUALITY
      );

    };
    image.src = imageUrl;
  };
  reader.readAsDataURL(file);
}

function displayInfo(label, file) {
  const p = document.createElement('p');
  p.classList.add('text-secondary', 'm-0', 'fs-6');
  p.innerText = `${label} ${readableBytes(file.size)}`;
  $divImagen.append(p);
}

function readableBytes(bytes) {
  const i = Math.floor(Math.log(bytes) / Math.log(1024)),
  sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
  return (bytes / Math.pow(1024, i)).toFixed(2) + ' ' + sizes[i];
}

function calculateSize(img, maxWidth, maxHeight) {
  let width = img.width;
  let height = img.height;
  // calculate the width and height, constraining the proportions
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

async function FnAgregarArchivo(){
  try {
    vgLoader.classList.remove('loader-full-hidden');
    var archivo;
    if(document.getElementById('canvas')){
      archivo = document.querySelector("#canvas").toDataURL("image/jpeg");
    }else if(document.getElementById('fileImagen').files.length == 1){
      archivo = fileOrCanvasData = document.getElementById('fileImagen').files[0];
    }else{
      throw new Error('No se reconoce el archivo');
    }
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
        timer:2000 
      });
      setTimeout(() => { location.reload(); }, 1000);
    }else {
      Swal.fire({
        title: 'Aviso',
        text: datos.msg,
        icon: 'info',
        timer: 2000
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
      timer:2000
    });
  }
}

const FnModalModificarArchivo = async (id)=>{
  document.getElementById('txtArchivoId').value = id;
  
  const formData = new FormData();
  formData.append('id', id);
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
    document.getElementById('txtTitulo2').value = datos.data.titulo;
    document.getElementById('txtDescripcion2').value = datos.data.descripcion;
    document.getElementById('divImagen2').innerHTML = datos.data.nombre;
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
    // AGREGAR ARCHIVO SI SE HA CARGADO
    const fileInput = document.getElementById('fileImagen2');
    if (fileInput.files.length === 1) {
      formData.append('archivo', fileInput.files[0]); 
    }
    const response = await fetch('/gesman/update/ModificarArchivo.php', {
      method: 'POST',
      body: formData
    });
    if (!response.ok) {
      throw new Error(`${response.status} ${response.statusText}`);
    }
    const datos = await response.json();
    console.log(datos);
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
    console.log(id, refid);
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
  // Aquí podrías agregar lógica para guardar el nuevo título, por ejemplo, enviarlo a un servidor
  console.log("Nuevo título:", titulo);
}

function editarDescripcion(id) {
  const descripcion = document.getElementById(`descripcion-${id}`).innerText;
  // Aquí podrías agregar lógica para guardar la nueva descripción
  console.log("Nueva descripción:", descripcion);
}

function editarImagen(id) {
  const nuevaImagen = prompt("Introduce la URL de la nueva imagen:");
  if (nuevaImagen) {
    document.getElementById(`imagen-${id}`).src = nuevaImagen;
    // Aquí podrías agregar lógica para guardar la nueva imagen en el servidor
    console.log("Nueva imagen:", nuevaImagen);
  }
}

function FnResumenInforme(){
  id = document.getElementById('txtInformeId').value;
  console.log(0, id)
  if(id > 0){
      window.location.href='/informes/Informe.php?id='+id;
  }
  return false;
}

function FnListarInformes(){
  window.location.href='/informes/Informes.php';
  return false;
};


/** CAMBIO DE POSICIONES DE ACTIVIDADES */
// function FnCapturarActividad(item) {
//   const actividad = {
//     id: item.id,
//     infid: item.querySelector('#infidPadre').value,
//     ownid: item.querySelector('#ownidPadre').value,
//     tipo: item.querySelector('#tipoPadre').value,
//     actividad: item.querySelector('.accordion-header p').innerText.replace('\n', '').trim(),
//     diagnostico: item.querySelector('.diagnostico').innerText.trim(),
//     trabajos: item.querySelector('.trabajo').innerText.trim(),
//     observaciones: item.querySelector('.observacion').innerText.trim(),
//     archivos: [],
//     hijos: []
//   };
  
//   // OBTENER LAS IMÁGENES
//   const contenedorArchivos = item.querySelector('.contenedor-imagen');
//   if (contenedorArchivos) {
//     const archivos = contenedorArchivos.querySelectorAll('.d-flex.flex-column');
//     archivos.forEach(archivo => {
//       const archivoId = archivo.querySelector('span[data-bs-toggle="tooltip"][title="Editar"]').getAttribute('onclick').match(/\(([^)]+)\)/)[1]; 
//       actividad.archivos.push({
//         id: archivoId,
//         refid: actividad.id,
//         tabla: archivo.querySelector('#txtTabla') ? archivo.querySelector('#txtTabla').value : '',
//         nombre: archivo.querySelector('#imagenArchivo').getAttribute('src').split('/')[4],
//         titulo: archivo.querySelector('#tituloArchivo').textContent,
//         descripcion: archivo.querySelector('#descripcionArchivo').textContent,
//         tipo: archivo.querySelector('#txtTipoArchivo').value
//       });
//     });
//   }
  
//   // Capturar hijos
//   const hijos = item.querySelectorAll(`#accordion-container .accordion-item`); 
//   hijos.forEach(hijo => {
//     actividad.hijos.push(FnCapturarActividad(hijo));
//   });
  
//   return actividad;
// }

// function FnCapturarActividades() {
//   const actividades = [];
//   const items = document.querySelector('#accordion-container');
//   Array.from(items.children).forEach(actividadPadre => {
//     actividades.push(FnCapturarActividad(actividadPadre));
//   });
  
//   console.log(actividades);
//   return actividades;
// }

// // MOVER ACCORDION-ITEM HACIA ARRIBA
// async function FnMoverArriba(item) {
//   const prev = item.previousElementSibling;
//   if (prev) {
//     item.parentNode.insertBefore(item, prev);
//     await FnActualizarPosiciones();
//   }
// }

// // MOVER ACCORDION-ITEM HACIA ABAJO
// async function FnMoverAbajo(item) {
//   const next = item.nextElementSibling;
//   if (next) {
//     item.parentNode.insertBefore(next, item);
//     await FnActualizarPosiciones();
//   }
// }

// // Actualizar posiciones en el servidor
// async function FnActualizarPosiciones() {
//   const actividades = FnCapturarActividades(); 
  
//   try {
//     const response = await fetch('/informes/update/ModificarActividades.php', {
//       method: 'POST',
//       headers: {
//         'Content-Type': 'application/json'
//       },
//       body: JSON.stringify(actividades)
//     });

//     console.log(response);
    
//     // Verifica si la respuesta es correcta
//     if (!response.ok) {
//       throw new Error(response.statusText);
//     }

//     const data = await response.json();
//     console.log(data);
//   } catch (error) {
//     console.log(error);
//   }
// }

// // AGREGAR EVENTO A BOTONES
// function FnAgregarEventoBotones(item) {
//   const btnArriba = item.querySelector('.btn-arriba');
//   const btnAbajo = item.querySelector('.btn-abajo');
  
//   if (btnArriba) {
//     btnArriba.addEventListener('click', () => FnMoverArriba(item));
//   }
  
//   if (btnAbajo) {
//     btnAbajo.addEventListener('click', () => FnMoverAbajo(item));
//   }
// }

// // INICIALIZANDO ACTIVIDADES Y AGREGAR
// function FnInicializar() {
//   FnCapturarActividades();
//   const items = document.querySelectorAll('.accordion-item');

//   // Solo agregar eventos a los items principales
//   items.forEach(item => {
//     if (!item.querySelector('.accordion-item')) { // Asegúrate de que no tenga hijos
//       FnAgregarEventoBotones(item);
//     }
//   });
// }

// // Ejecutar la inicialización al cargar la página
// FnInicializar();


const actividades = [];
const items = document.querySelector('#accordion-container');
Array.from(items.children).forEach(elemento => {
  actividades.push(elemento.id, elemento.getAttribute('data-position'));
});
console.log(actividades);



