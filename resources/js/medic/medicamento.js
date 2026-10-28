
function toggleDetalle(id) {
    const detalle = document.getElementById('detalle-' + id);

    if (detalle) {
        detalle.classList.toggle('hidden');
    }
}

window.toggleDetalle = toggleDetalle;
