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
      // Calcular el tamaño nuevo basado en la dimensión máxima
      const [newWidth, newHeight] = calculateSize(image, MAX_WIDTH, MAX_HEIGHT);
      canvas.width = 720;  // Fijar ancho
      canvas.height = 720; // Fijar alto

      canvas.id = "canvas";
      
      // Limpiar el canvas y establecer un color de fondo
      context.clearRect(0, 0, canvas.width, canvas.height);
      context.fillStyle = 'rgba(255, 255, 255, 1)'; // Color de fondo (blanco)
      context.fillRect(0, 0, canvas.width, canvas.height); // Dibujar el rectángulo

      // Ajustar la imagen en el canvas, manteniendo la proporción
      let scaleFactor = Math.min(canvas.width / newWidth, canvas.height / newHeight);
      let adjustedWidth = newWidth * scaleFactor;
      let adjustedHeight = newHeight * scaleFactor;

      // Centrar la imagen
      let x = (canvas.width - adjustedWidth) / 2;
      let y = (canvas.height - adjustedHeight) / 2;

      // Dibujar la imagen
      context.drawImage(image, x, y, adjustedWidth, adjustedHeight);

      // Agregar texto como marca de agua
      context.strokeStyle = 'rgba(216, 216, 216, 0.7)'; // Color del texto (blanco con opacidad)
      context.font = '15px Verdana'; // Fuente y tamaño del texto
      context.strokeText("GPEM SAC", 10, 710); // Texto y posición

      // Convertir el canvas a un blob y mostrar la imagen en la interfaz
      canvas.toBlob(
        (blob) => {
          displayInfo('Original: ', file);
          displayInfo('Comprimido: ', blob);
        },
        'image/png', // Usar PNG para preservar la transparencia
        QUALITY
      );

      // Mostrar la imagen en la interfaz
      const imgElement = document.createElement('img');
      imgElement.src = canvas.toDataURL('image/png'); // Asegúrate de usar PNG
      imgElement.style.width = '100%'; // Ajustar al 100% del contenedor
      imgElement.style.height = 'auto'; // Mantener la relación de aspecto
      $divImagen.appendChild(imgElement); // Agregar la imagen a la interfaz
    };
    image.src = imageUrl;
  };
  reader.readAsDataURL(file);
}