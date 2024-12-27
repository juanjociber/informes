// INICIALIZANDO VARIABLES PARA MODAL GLOBAL
var modalEquipo
const vgLoader = document.querySelector('.container-loader-full');

window.onload = function() {
  modalEquipo = new bootstrap.Modal(document.getElementById('modalEquipo'), { keyboard: false });
  // document.getElementById('MenuInformes').classList.add('menu-activo','fw-bold');
  vgLoader.classList.add('loader-full-hidden');
};

// ABRIR MODAL PARA REGISTRAR IMAGEN
const FnModalInformeAgregarArchivo = () => {
  const modal = new bootstrap.Modal(document.getElementById('modalAgregarImagen'), { keyboard: false });
  modal.show();
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
    //console.log('El archivo', file.name, 'Tipo de archivo no permitido.');
  }

  if (!isValidFileSize(file)) {
    //console.log('El archivo', file.name, 'El tamaño del archivo excede los 3MB.');
  }

  while ($divImagen.firstChild) {
    $divImagen.removeChild($divImagen.firstChild);
  }

  if (file.type.startsWith('image/')) {
    displayImage(file);
  }
  // console.log('Nombre del archivo:', file.name);
  // console.log('Tipo del archivo:', file.type);
  // console.log('Tamaño del archivo:', file.size, 'bytes');
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

async function FnAgregarInformeArchivo() {
  try {
    vgLoader.classList.remove('loader-full-hidden');
    let archivo;
    if (document.getElementById('canvas')) {
      archivo = document.querySelector("#canvas").toDataURL("image/jpeg");
    } else if (document.getElementById('fileImagen').files.length === 1) {
      archivo = document.getElementById('fileImagen').files[0];
    } else {
      throw new Error('No se reconoce el archivo');
    }
    const formData = new FormData();
    formData.append('refid', document.getElementById('txtInformeId').value);
    formData.append('titulo', document.getElementById('txtTitulo').value);
    formData.append('descripcion', document.getElementById('txtDescripcion').value);
    formData.append('archivo', archivo);
    formData.append('tabla', 'INFE');
    const response = await fetch('/gesman/insert/AgregarArchivo.php', {
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
    setTimeout(() => { 
      vgLoader.classList.add('loader-full-hidden'); 
    }, 500);
    document.getElementById('msjAgregarImagen').innerHTML = `<div class="alert alert-danger m-0 p-1 text-center" role="alert">${error.message}</div>`;
  }
}

//ELIMINAR ARCHIVO
const FnEliminarInformeArchivo = async (id) => {
  try {
    vgLoader.classList.remove('loader-full-hidden');
    const formData = new FormData();
    formData.append('id', id);
    formData.append('refid', document.getElementById('txtInformeId').value);
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
      await Swal.fire({
        title: "¡Éxito!",
        text: datos.msg,
        icon: "success",
        timer: 2000
      });
      setTimeout(() => { 
        location.reload(); 
      }, 1000);
    } else {
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
      title: "Aviso",
      text: error.message,
      icon: "error",
      timer: 2000
    });
  }
};

// FUNCIÓN BUSCAR INFORME POR 'id'
const FnModalModificarInformeEquipo = async (id)=>{
  modalEquipo.show();
};

$(document).ready(function() {
  $('#cbEquipo').select2({
    dropdownParent: $('#modal-body'),
    width: 'resolve', //Personalizar el alto del select, aplicar estilo.
    ajax: {
        delay: 450, //Tiempo de demora para buscar
        url: '/gesman/search/ListarEquipos.php',
        type: 'POST',
        dataType: 'json',
        data: function (params) {
          return {
              nombre: params.term // parametros a enviar al server. params.term captura lo que se escribe en el input
          };
        },
        processResults: function (datos) {
          console.log(datos);
          return {
            results:datos.data.map(function(elem) {
                return {
                    id:elem.id,
                    text:elem.nombre,
                };
            })
          }
        },
        cache: true
    },
    placeholder: 'Seleccionar',
    // allowClear: true, 
    minimumInputLength:1 //Caracteres minimos para buscar
  });
});

// FUNCIÓN MÓDIFICAR EQUIPO
const FnModificarInformeEquipo = async () => {
  try {
    vgLoader.classList.remove('loader-full-hidden');
    id = document.getElementById('txtInformeId').value;
    equnombre = document.getElementById('cbEquipo').options[document.getElementById('cbEquipo').selectedIndex].text;
    equmarca = document.getElementById('txtEquMarca2').value;
    equmodelo = document.getElementById('txtEquModelo2').value;
    equserie = document.getElementById('txtEquSerie2').value;
    equdatos = document.getElementById('txtEquDatos2').value;
    equkm = document.getElementById('txtEquKm2').value;
    equhm = document.getElementById('txtEquHm2').value;

    const formData = new FormData();
    formData.append('Id', id);
    formData.append('Equid', document.getElementById('cbEquipo').value);
    formData.append('EquNombre', equnombre);
    formData.append('EquMarca', equmarca);
    formData.append('EquModelo', equmodelo);
    formData.append('EquSerie', equserie);
    formData.append('EquDatos', equdatos);
    formData.append('EquKm', equkm);
    formData.append('EquHm', equhm);

    const response = await fetch('/informes/update/ModificarInformeEquipo.php', {
      method: 'POST',
      body: formData
    });
    if (!response.ok) {
      throw new Error(`Error: ${response.status} ${response.statusText}`);
    }
    const datos = await response.json();
    // Actualizar los campos del equipo
    document.querySelector('#txtEquNombre1').textContent = equnombre;
    document.querySelector('#txtEquMarca1').textContent = equmarca;
    document.querySelector('#txtEquModelo1').textContent = equmodelo;
    document.querySelector('#txtEquSerie1').textContent = equserie;
    document.querySelector('#txtEquDatos1').textContent = equdatos;
    document.querySelector('#txtEquKm1').textContent = equkm;
    document.querySelector('#txtEquHm1').textContent = equhm;
    // Mostrar el SweetAlert
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
    setTimeout(() => { 
      vgLoader.classList.add('loader-full-hidden'); 
    }, 500);
    await Swal.fire({
      title: "Aviso",
      text: error.message,
      icon: "error",
      timer: 2000
    });
  }
};

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
}

document.getElementById('editarInformeEquipo').addEventListener('click', function() {
  document.getElementById('editarDatoEquipo').querySelector('g').setAttribute('stroke', '#0d6efd');
});

document.getElementById('adjuntarInformeEquipoArchivo').addEventListener('click', function() {
  document.getElementById('Archivo').setAttribute('stroke', '#6c757d');
});


