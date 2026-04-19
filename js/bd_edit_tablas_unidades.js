document.addEventListener('DOMContentLoaded', function () {
    actualizarTabla2();
    document.querySelector('tbody')?.addEventListener('input', e => {
        if (e.target.classList.contains('pct-input')) actualizarTabla2();
    });
});

// Cascada de unidades en edición
document.getElementById('id_tipo_unidad_edit')?.addEventListener('change', function () {
    const idTipo = this.value;
    const selectUnidad = document.getElementById('id_unidad_edit');

    selectUnidad.innerHTML = '<option value="">Cargando...</option>';
    selectUnidad.disabled = true;

    fetch(`api/obtener_unidades.php?id_tipo=${idTipo}`)
        .then(r => r.json())
        .then(unidades => {
            selectUnidad.innerHTML = '<option value="">Seleccionar</option>';
            unidades.forEach(u => {
                const option = document.createElement('option');
                option.value = u.id_unidad;
                option.textContent = u.numero_unidad || u.numero;
                selectUnidad.appendChild(option);
            });
            selectUnidad.disabled = false;
        });
});