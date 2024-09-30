function showToast(message, color) {
    // Crear el contenedor del toast si no existe
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
      toastContainer = document.createElement('div');
      toastContainer.className = 'toast-container position-fixed';
      toastContainer.style.top='1rem';
      toastContainer.style.right='1rem';
      toastContainer.style.zIndex='1060';
      document.body.appendChild(toastContainer);
    }
  
    // Seleccionar o crear un toast
    let toastEl = document.querySelector('.toast');
    if (!toastEl) {
        toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-white border-0 ${color}`;
        toastEl.role = 'alert';
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');
      
        // No agregar data-bs-autohide para evitar cierre automático  
        // Crear el contenido del toast
        toastEl.innerHTML = `
        <div class="d-flex">
          <div class="toast-body"></div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" aria-label="Close"></button>
        </div>`;
  
        // Agregar el toast al contenedor
        toastContainer.appendChild(toastEl);
  
        // Agregar listener para el botón de cierre
        toastEl.querySelector('.btn-close').addEventListener('click', function () {
            // Limpia el timeout si el usuario cierra manualmente
            clearTimeout(toastEl.dataset.timeoutId);
            toastEl.remove();
        });
    }
  
    // Actualiza el contenido del toast
    const toastBody = toastEl.querySelector('.toast-body');
    toastBody.textContent = message;
  
    // Crea una instancia de toast y muestra el toast
    const toast = new bootstrap.Toast(toastEl, { autohide: false });
    toast.show();
  
    // Eliminar el toast después de 3 segundos si no se ha cerrado manualmente
    const timeoutId = setTimeout(() => {
      if (toastEl.parentNode) { // Verifica si el toast aún está en el DOM
        toastEl.remove();
      }
    }, 3000);
  
    // Guarda el ID del timeout en el dataset del elemento
    toastEl.dataset.timeoutId = timeoutId;
  }