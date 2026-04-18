// Cascada: Tipo Unidad -> Número Unidad
document.addEventListener('DOMContentLoaded', function() {
    const selectTipo = document.getElementById('id_tipo_unidad');
    const selectUnidad = document.getElementById('id_unidad');
    
    if (selectTipo && selectUnidad) {
        selectTipo.addEventListener('change', function() {
            const idTipo = this.value;
            
            // Limpiar y deshabilitar select de unidades
            selectUnidad.innerHTML = '<option value="">Cargando...</option>';
            selectUnidad.disabled = true;
            
            if (!idTipo) {
                selectUnidad.innerHTML = '<option value="">Seleccione tipo</option>';
                return;
            }
            
            // Cargar unidades por tipo
            fetch(`api/obtener_unidades.php?id_tipo=${idTipo}`)
                .then(r => r.json())
                .then(unidades => {
                    selectUnidad.innerHTML = '<option value="">Seleccionar</option>';
                    unidades.forEach(u => {
                        const option = document.createElement('option');
                        option.value = u.id_unidad;
                        option.textContent = u.numero_unidad;
                        selectUnidad.appendChild(option);
                    });
                    selectUnidad.disabled = false;
                })
                .catch(err => {
                    console.error('Error cargando unidades:', err);
                    selectUnidad.innerHTML = '<option value="">Error al cargar</option>';
                });
        });
    }
});
