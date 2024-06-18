// JavaScript para añadir un GIF de fondo al login
document.addEventListener('DOMContentLoaded', function() {
    const gifBackground = document.getElementById('gif-background');

    // Cambia el fondo animado (GIF)
    gifBackground.style.backgroundImage = 'url("./videos/Hacker.gif")';
    gifBackground.style.backgroundSize = 'cover'; // Ajusta el tamaño para cubrir el área
    gifBackground.style.position = 'fixed';
    gifBackground.style.top = '0';
    gifBackground.style.left = '0';
    gifBackground.style.width = '100%';
    gifBackground.style.height = '100%';
    gifBackground.style.zIndex = '-1'; // Coloca el fondo detrás de los demás elementos
});

// Función para mostrar el popup
function mostrarPopup() {
    // Obtener el contenedor del popup
    console.log("Activar!");
    var popupContainer = document.getElementById("popupContainer");

    // Mostrar el contenedor del popup
    popupContainer.style.display = "block";
}

// Función para cerrar el popup
function cerrarPopup() {
    // Obtener el contenedor del popup
    console.log("Cerrar!");
    var popupContainer = document.getElementById("popupContainer");

    // Ocultar el contenedor del popup
    popupContainer.style.display = "none";
}

// Agregar un event listener al botón para mostrar el popup
document.getElementById("mostrarPopupBtn").addEventListener("click", mostrarPopup);

// Agregar un event listener al botón para cerrar el popup
document.getElementById("cerrarPopupBtn").addEventListener("click", cerrarPopup);


