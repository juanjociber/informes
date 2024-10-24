const vgLoader = document.querySelector('.container-loader-full');

window.onload = function() {
  // document.getElementById('MenuInformes').classList.add('menu-activo', 'fw-bold');
  vgLoader.classList.add('loader-full-hidden');
};

function FnListarInformes() {
  window.location.href = '/informes/Informes.php';
  return false;
}

function FnEditarInforme() {
  let informe = document.getElementById('idInforme').value;
  if (informe > 0) {
      window.location.href = '/informes/EditarInforme.php?id=' + informe;
  }
  return false;
}

function FnDescargarInforme() {
  let id = document.getElementById('idInforme').value;
  if (id > 0) {
      window.location.href = '/informes/pdf/DescargarInforme.php?id=' + id;
  }
  return false;
}

function FnModalFinalizarInforme() {
  let modalFinalizarInforme = new bootstrap.Modal(document.getElementById('modalFinalizarInforme'), {
    keyboard: false
  });
  modalFinalizarInforme.show();
}

async function FnFinalizarInforme() {
  vgLoader.classList.remove('loader-full-hidden');
  try {
    const formData = new FormData();
    formData.append('id', document.getElementById('idInforme').value);
    const response = await fetch('/informes/update/FinalizarInforme.php', {
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
    setTimeout(() => location.reload(), 1000);
  } catch (ex) {
      showToast(ex.message, 'bg-danger');
  } finally {
      setTimeout(() => {
        vgLoader.classList.add('loader-full-hidden');
      }, 500);
  }
}

// MOSTRAR TÍTULOS
function mostrarTitulo(clase) {
  const titulos = document.querySelectorAll(`.${clase}`);
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
