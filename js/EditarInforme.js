const vgLoader = document.querySelector('.container-loader-full')
window.onload = function() {
  // document.getElementById('MenuInformes').classList.add('menu-activo','fw-bold');
  vgLoader.classList.add('loader-full-hidden');
};
// FUNCIÓN SELECT PERSONALIZADO
const cargaSelect = () => {
  const initCustomSelect = (inputId, listId, hiddenInputId) => {
    const selectInput = document.getElementById(inputId);
    const selectList = document.getElementById(listId);
    const hiddenInput = document.getElementById(hiddenInputId);
    if (!selectInput || !selectList || !hiddenInput) {
      return;
    }
    const selectItems = selectList.getElementsByClassName("custom-select-item");
    // MOSTRAR / OCULTAR LISTA AL HACER CLIC EN INPUT
    selectInput.addEventListener("click", function () {
      selectList.style.display = selectList.style.display === "block" ? "none" : "block";
    });

    // SELECCIONAR UN ELEMENTO DE LA LISTA
    Array.from(selectItems).forEach((item) => {
      item.addEventListener("click", function () {
        selectInput.value = this.textContent.trim();
        selectInput.dataset.value = this.dataset.value; // Guardar idsupervisor

        // Obtener perid del input hidden dentro del item seleccionado
        const peridValue = this.querySelector(".perid-value").value;
        hiddenInput.value = peridValue; // Asignar perid al input oculto
        selectList.style.display = "none";
      });
    });

    // OCULTAR LISTA SI SE HACE CLIC FUERA
    document.addEventListener("click", function (event) {
      if (!event.target.closest(".custom-select-wrapper")) {
        selectList.style.display = "none";
      }
    });
    // FILTRAR ELEMENTOS DE LA LISTA AL ESCRIBIR EN EL INPUT
    selectInput.addEventListener("input", function () {
      const filter = selectInput.value.toLowerCase();
      let textoEncontrado = false;

      Array.from(selectItems).forEach((item) => {
        const text = item.textContent.toLowerCase();
        if (text.includes(filter)) {
          item.style.display = "";
          textoEncontrado = true;
        } else {
          item.style.display = "none";
        }
      });
      selectList.style.display = "block";
      // LIMPIAR EL INPUT SI NO HAY CONCIDENCIAS
      if (!textoEncontrado) {
        selectInput.value = "";
        hiddenInput.value = ""; // También limpiar el perid
        Array.from(selectItems).forEach((item) => {
          item.style.display = "";
        });
      }
    });
  };
  // INICIALIZANDO SELECT CON PARAMETRO PARA perid
  initCustomSelect("cbCliContacto", "contactoList", "txtPerId");
  initCustomSelect("cbSupervisor", "supervisorList", "txtPerId");
};

// LLAMANDO FUNCIÓN CARGA DE SELECT
document.addEventListener("DOMContentLoaded", cargaSelect);


// FUNCIÓN GUARDAR DATOS GENERALES
const FnModificarInforme = async () => {
  try {
    vgLoader.classList.remove('loader-full-hidden');
    const formData = new FormData();      
    formData.append('id', document.querySelector('#txtInformeId').value);
    formData.append('supid', document.querySelector('#txtPerId').value);
    formData.append('fecha', document.querySelector('#dpfecha').value.trim());
    formData.append('clicontacto', document.querySelector('#cbCliContacto').value.trim()); 
    formData.append('clidireccion', document.querySelector('#txtCliDireccion').value.trim()); 
    formData.append('supervisor', document.querySelector('#cbSupervisor').value.trim());

    const response = await fetch('/informes/update/ModificarInforme.php', {
      method: 'POST',
      body: formData
    });
    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }
    const datos = await response.json();
    if(!datos.res){
      throw new Error(datos.msg);
    }    
    setTimeout(() => { vgLoader.classList.add('loader-full-hidden'); }, 300);
    await Swal.fire({
      title: "¡Éxito!",
      text: datos.msg,
      icon: "success",
      timer: 2000
    });
    setTimeout(() => { location.reload(); }, 1000);
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
}

document.getElementById('guardarInforme').addEventListener('click', function() {
  document.getElementById('editarInforme').querySelector('g').setAttribute('stroke', '#FFFFFF');
});