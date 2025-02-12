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
  vgLoader.classList.remove('loader-full-hidden');
  try {
    console.log('clic')
    id = document.getElementById('txtInformeId').value;
    equnombre = document.getElementById('cbEquipo').options[document.getElementById('cbEquipo').selectedIndex].text;
    equmarca = document.getElementById('txtEquMarca2').value;
    equmodelo = document.getElementById('txtEquModelo2').value;
    equserie = document.getElementById('txtEquSerie2').value;
    equdatos = document.getElementById('txtEquDatos2').value;
    equreferencia = document.getElementById('txtEquReferencia2').value; 
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
    formData.append('EquReferencia', equreferencia);
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
    document.querySelector('#txtEquReferencia1').textContent = equreferencia;
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


/**=============================================
 * FUNCIÓN: RECORTAR, ROTAR, ZOOM y TRAZAR
 * =============================================
 */
function FnInitCroppieDrawing(instanceId) {
  let croppieInstance;
  const controles = document.getElementById(`controles${instanceId}`);
  const cropArea = document.getElementById(`crop-area${instanceId}`);
  const canvas = document.getElementById(`dibujoCanvas${instanceId}`);
  const ctx = canvas.getContext('2d');
  let drawing = false;
  let img;
  let lastX = 0, lastY = 0, lastTime = 0;

  // Permite darle precisión cuando se dibuja el canvas
  function calcularVelocidad(x, y) {
    const now = Date.now();
    const deltaX = x - lastX;
    const deltaY = y - lastY;
    const distancia = Math.sqrt(deltaX ** 2 + deltaY ** 2);
    const tiempo = now - lastTime || 1;
    const velocidad = distancia / tiempo;
    lastX = x;
    lastY = y;
    lastTime = now;
    return velocidad;
  }

  document.getElementById(`fileImagen${instanceId}`).addEventListener('change', function (event) {
    let reader = new FileReader();
    reader.onload = function (e) {
      if (croppieInstance) croppieInstance.destroy();
      croppieInstance = new Croppie(cropArea, {
        viewport: { width: 300, height: 300, type: 'square' },
        boundary: { width: 400, height: 400 },
        showZoomer: true,
        enableOrientation: true
      });
      croppieInstance.bind({ url: e.target.result });
      controles.classList.remove('oculto');
      cropArea.classList.remove('oculto');
    };
    reader.readAsDataURL(event.target.files[0]);
  });

  document.getElementById(`rotateLeft${instanceId}`).addEventListener('click', () => croppieInstance.rotate(-90));
  document.getElementById(`rotateRight${instanceId}`).addEventListener('click', () => croppieInstance.rotate(90));

  document.getElementById(`save${instanceId}`).addEventListener('click', function () {
    croppieInstance.result({ type: 'base64', size: 'viewport', format: 'jpeg' }).then(function (base64) {
      img = new Image();
      img.onload = function () {
        canvas.width = img.width;
        canvas.height = img.height;
        ctx.drawImage(img, 0, 0);
        canvas.classList.remove('oculto');
        document.getElementById(`trazado${instanceId}`).classList.remove('oculto');
      };
      img.src = base64;
    });
    cropArea.classList.add('oculto');
    controles.classList.add('oculto');
  });

  function getCoordenadas(event) {
    let x, y;
    if (event.touches) {
      const touch = event.touches[0];
      const rect = canvas.getBoundingClientRect();
      x = touch.clientX - rect.left;
      y = touch.clientY - rect.top;
    } else {
      x = event.offsetX;
      y = event.offsetY;
    }
    return { x, y };
  }

  function iniciarDibujo(event) {
    drawing = true;
    const { x, y } = getCoordenadas(event);
    ctx.beginPath();
    ctx.moveTo(x, y);
    lastX = x;
    lastY = y;
    lastTime = Date.now();
  }

  function dibujar(event) {
    if (!drawing) return;
    const { x, y } = getCoordenadas(event);
    const velocidad = calcularVelocidad(x, y);
    ctx.strokeStyle = document.getElementById(`colorPicker${instanceId}`).value;
    ctx.lineWidth = Math.max(2, Math.min(5, 5 - velocidad * 2));
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    const pasos = Math.max(1, Math.round(velocidad * 10));
    for (let i = 0; i < pasos; i++) {
      const interpolX = lastX + (x - lastX) * (i / pasos);
      const interpolY = lastY + (y - lastY) * (i / pasos);
      ctx.lineTo(interpolX, interpolY);
      ctx.stroke();
    }
    lastX = x;
    lastY = y;
    ctx.beginPath();
    ctx.moveTo(lastX, lastY);
  }
  function finalizarDibujo() { drawing = false; }

  canvas.addEventListener('mousedown', iniciarDibujo);
  canvas.addEventListener('mousemove', dibujar);
  canvas.addEventListener('mouseup', finalizarDibujo);
  canvas.addEventListener('mouseleave', finalizarDibujo);
  canvas.addEventListener('touchstart', (event) => { iniciarDibujo(event); event.preventDefault(); });
  canvas.addEventListener('touchmove', (event) => { dibujar(event); event.preventDefault(); });
  canvas.addEventListener('touchend', finalizarDibujo);
  canvas.addEventListener('touchcancel', finalizarDibujo);

  document.getElementById(`clearCanvas${instanceId}`).addEventListener('click', () => {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(img, 0, 0);
  });

  document.getElementById(`guardarTrazo${instanceId}`).addEventListener('click', function () {
    document.getElementById(`trazado${instanceId}`).classList.add('oculto');
    document.getElementById(`btn-guardar-archivo${instanceId}`).removeAttribute('disabled');
    const newCanvas = document.createElement('canvas');
    newCanvas.setAttribute('id', `canvas${instanceId}`);
    const newCtx = newCanvas.getContext('2d');
    newCanvas.width = canvas.width;
    newCanvas.height = canvas.height;
    newCtx.drawImage(img, 0, 0);
    newCtx.drawImage(canvas, 0, 0);
    newCtx.font = '15px Verdana';
    newCtx.fillStyle = 'rgba(216, 216, 216, 0.7)';
    newCtx.fillText("GPEM SAC", newCanvas.width - 100, newCanvas.height - 10);
    document.getElementById(`divImagen${instanceId}`).innerHTML = '';
    document.getElementById(`divImagen${instanceId}`).appendChild(newCanvas);
    canvas.classList.add('oculto');
  });
}
FnInitCroppieDrawing('');
// FnInitCroppieDrawing('2');





