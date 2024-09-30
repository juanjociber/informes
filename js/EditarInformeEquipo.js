// INICIALIZANDO VARIABLES PARA MODAL GLOBAL
var modalEquipo
const vgLoader = document.querySelector('.container-loader-full');

window.onload = function() {
  modalEquipo = new bootstrap.Modal(document.getElementById('modalEquipo'), { keyboard: false });
  document.getElementById('MenuInformes').classList.add('menu-activo','fw-bold');
  vgLoader.classList.add('loader-full-hidden');
};

// FUNCIÓN BUSCAR EQUIPO POR ID
const fnBuscarEquipoPorId = async (id)=>{
  modalEquipo.show();
};

// FUNCIÓN MÓDIFICAR EQUIPOS
const FnModificarInformeEquipo = async () => {
  try {
    vgLoader.classList.remove('loader-full-hidden');
    const id = document.getElementById('idInforme').value;
    const equnombre = document.getElementById('txtEquNombre').value.trim();
    const equmarca = document.getElementById('txtEquMarca').value.trim();
    const equmodelo = document.getElementById('txtEquModelo').value.trim();
    const equserie = document.getElementById('txtEquSerie').value.trim();
    const equkm = document.getElementById('txtEquKm').value.trim();
    const equhm = document.getElementById('txtEquHm').value.trim();

    const formData = new FormData();
    formData.append('Id', id);
    formData.append('EquNombre', equnombre);
    formData.append('EquMarca', equmarca);
    formData.append('EquModelo', equmodelo);
    formData.append('EquSerie', equserie);
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
    setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
    if (!datos.res) {
      throw new Error(datos.msg);
    }
    // Actualizar los campos del equipo
    document.querySelector('#nombreEquipo').textContent = equnombre;
    document.querySelector('#marcaEquipo').textContent = equmarca;
    document.querySelector('#modeloEquipo').textContent = equmodelo;
    document.querySelector('#serieEquipo').textContent = equserie;
    document.querySelector('#kilometrajeEquipo').textContent = equkm;
    document.querySelector('#horasMotorEquipo').textContent = equhm;
    // Mostrar el SweetAlert
    await Swal.fire({
      title: "Información de servidor",
      text: datos.msg,
      icon: "success",
      timer: 2000
    });
    setTimeout(() => { location.reload(); }, 100);
  } catch (error) {
    setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
    await Swal.fire({
      title: "Información de servidor",
      text: error.message,
      icon: "error",
      timer: 2000
    });
  }
};

//ELIMINAR ARCHIVO
const fnEliminarImagen = async (id) => {
  try {
    vgLoader.classList.remove('loader-full-hidden');
    const formData = new FormData();
    formData.append('id', id);
    
    const response = await fetch('/informes/delete/EliminarArchivo.php', {
      method: 'POST',
      body: formData,
      headers: {
        'Accept': 'application/json'
      }
    });
    const result = await response.json();
    setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);

    if (result.res) {
      const elemento = document.getElementById(id);
      if (elemento) {
        elemento.remove();
      }
      await Swal.fire({
        title: "Información de servidor",
        text: result.msg,
        icon: "success",
        timer: 2000
      });
      setTimeout(() => { location.reload(); }, 100);
    } else {
      await Swal.fire({
        title: "Información de servidor",
        text: result.msg,
        icon: "error",
        timer: 2000
      });
    }
  } catch (error) {
    setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
    await Swal.fire({
      title: "Información de servidor",
      text: `Error: ${error.message}`,
      icon: "error",
      timer: 2000
    });
  }
};

// ABRIR MODAL PARA REGISTRAR IMAGEN
const fnAbrirModalRegistrarImagen = () => {
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

async function FnAgregarImagen() {
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
    formData.append('refid', document.getElementById('idInforme').value);
    formData.append('titulo', document.getElementById('txtTitulo').value);
    formData.append('descripcion', document.getElementById('txtDescripcion').value);
    formData.append('archivo', archivo);
    formData.append('tabla', 'INFE');
    //console.log('Datos enviados:', Object.fromEntries(formData.entries()));
    const response = await fetch('/informes/insert/AgregarArchivo.php', {
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
    setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
    // Mostrar el SweetAlert
    await Swal.fire({
      title: "Éxito",
      text: datos.msg,
      icon: "success",
      timer: 2000
    });
    setTimeout(() => { location.reload(); }, 100);
  } catch (error) {
    setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
    document.getElementById('msjAgregarImagen').innerHTML = `<div class="alert alert-danger m-0 p-1 text-center" role="alert">${error.message}</div>`;
  }
}

function FnResumenInforme(){
  id = document.getElementById('idInforme').value;
  if(id > 0){
      window.location.href='/informes/Informe.php?id='+id;
  }
  return false;
}

function FnListarInformes(){
  window.location.href='/informes/Informes.php';
  return false;
}


// Acceso a los elementos del DOM
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const photo = document.getElementById('photo');
const captureButton = document.getElementById('capture');

// Acceso a la cámara
navigator.mediaDevices.getUserMedia({ video: true })
    .then(stream => {
        video.srcObject = stream;
    })
    .catch(err => {
        console.error("Error al acceder a la cámara: ", err);
    });

// Captura de la imagen
captureButton.addEventListener('click', () => {
    const context = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    const dataURL = canvas.toDataURL('image/png');
    photo.setAttribute('src', dataURL);
});


