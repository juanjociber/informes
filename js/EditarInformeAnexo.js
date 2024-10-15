const vgLoader = document.querySelector('.container-loader-full');
window.onload = function() {
  document.getElementById('MenuInformes').classList.add('menu-activo','fw-bold');
  vgLoader.classList.add('loader-full-hidden');
};
/*
===================================
 CARGA DE IMÁGENES
===================================
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

async function FnAgregarArchivo() {
  try {
    vgLoader.classList.remove('loader-full-hidden');
    var archivo;
    if (document.getElementById('canvas')) {
      archivo = document.querySelector("#canvas").toDataURL("image/jpeg");
    } else if (document.getElementById('fileImagen').files.length == 1) {
      archivo = fileOrCanvasData = document.getElementById('fileImagen').files[0];
    } else {
      throw new Error('No se reconoce el archivo');
    }
    const formData = new FormData();
    formData.append('refid', document.getElementById('txtIdInforme').value);
    formData.append('titulo', document.getElementById('txtTitulo').value);
    formData.append('descripcion', document.getElementById('txtDescripcion').value);
    formData.append('archivo', archivo);
    formData.append('tabla', 'INFA'); 

    const response = await fetch('/informes/insert/AgregarArchivo.php', {
      method: 'POST',
      body: formData
    });

    if (!response.ok) {throw new Error(`${response.status} ${response.statusText}`);}
    const datos = await response.json();
    if (!datos.res) {throw new Error(datos.msg);}
    setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
    Swal.fire({
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

//ELIMINAR ARCHIVO
const FnEliminarArchivo = async (id) => {
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
        Swal.fire({
          title: "Aviso",
          text: result.msg,
          icon: "success",
          timer: 2000
        });
        setTimeout(() => { location.reload();}, 100);
    } else {
      Swal.fire({
        title: "Información",
        text: result.msg,
        icon: "error",
        timer: 2000
      });
    }
  } catch (error) {
      setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
      await Swal.fire({
        title: "Información",
        text: `${error.message}`,
        icon: "error",
        timer: 2000
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