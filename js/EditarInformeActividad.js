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
FnInitCroppieDrawing('2');

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
    const archivoInput = document.getElementById('fileImagen2');
    const canvas = document.getElementById('canvas2');
    let archivo = null;
    if (canvas) {
      archivo = canvas.toDataURL("image/jpeg");
    } else if (archivoInput && archivoInput.files.length === 1) {
      archivo = archivoInput.files[0];
    }
    formData.append('id', document.getElementById('txtArchivoId').value);
    formData.append('titulo', document.getElementById('txtTitulo2').value);
    formData.append('descripcion', document.getElementById('txtDescripcion2').value);
    formData.append('tipo','INFD')

    if (archivo) {
      formData.append('archivo', archivo);
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
let sortableActividades;

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

// Escuchar cambios en el tamaño de la ventana
const mediaQuery = window.matchMedia('(min-width: 768px)');
mediaQuery.addEventListener('change', (e) => {
  FnInitSorteable();
});








