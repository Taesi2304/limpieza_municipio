//  TABLAS DINÁMICAS - Tabla 1 y Tabla 2
//  Lógica para mostrar colonias y calcular efectividad
document.addEventListener('DOMContentLoaded', function() {
    const btnGuardar = document.getElementById('btn_guardar');

    // Campos obligatorios para habilitar el botón guardar
    const camposObligatorios = [
        '[name="fecha_orden"]',
        '#fecha_captura',
        '[name="turno"]',
        '#id_ruta',
        '[name="id_despachador"]',
        '[name="id_chofer"]',
        '#id_tipo_unidad',
        '#id_unidad',
        '[name="cantidad_kg"]',
        '[name="cantidad_puches"]',
        '#diesel_inicio',
        '#diesel_final',
    ];

    function validarFormulario() {
        const todoOk = camposObligatorios.every(sel => {
            const el = document.querySelector(sel);
            return el && !el.disabled && el.value.trim() !== '';
        });
        btnGuardar.disabled = !todoOk;
    }

    // Escuchar todos los campos del formulario
    document.getElementById('formulario')?.addEventListener('input', validarFormulario);
    document.getElementById('formulario')?.addEventListener('change', validarFormulario);

    const selectRuta = document.getElementById('id_ruta');
    const tablaColoniasWrapper = document.getElementById('tabla_colonias_wrapper');
    const tbodyColonias = document.getElementById('tbody_colonias');
    
    // Al cambiar la ruta, cargar colonias
    selectRuta?.addEventListener('change', function() {
        const idRuta = this.value;
        
        if (!idRuta) {
            tablaColoniasWrapper.style.display = 'none';
            return;
        }
        
        // Hacer petición AJAX para obtener colonias
        fetch(`api/obtener_colonias.php?id_ruta=${idRuta}`)
            .then(response => response.json())
            .then(colonias => {
                renderTablaColonias(colonias);
                tablaColoniasWrapper.style.display = 'block';
                actualizarTabla2();
            })
            .catch(error => {
                console.error('Error cargando colonias:', error);
                alert('Error al cargar colonias');
            });
    });
    
    // Renderizar Tabla 1 (Colonias)
    function renderTablaColonias(colonias) {
        tbodyColonias.innerHTML = '';
        
        colonias.forEach((colonia, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="text-center fw-bold">${index + 1}</td>
                <td>${colonia.nombre_colonia}</td>
                <td class="text-center">
                    <input type="number"
                           class="form-control form-control-sm pct-input"
                           name="pct_colonia[${colonia.id_colonia}]"
                           data-id-colonia="${colonia.id_colonia}"
                           min="0"
                           max="100"
                           step="0.1"
                           value="0"
                           placeholder="0-100">
                </td>
                <td class="text-center">${parseInt(colonia.habitantes).toLocaleString()}</td>
            `;
            tbodyColonias.appendChild(tr);
        });
    }
    
    // Delegación: recalcular Tabla 2 al editar cualquier % de colonia
    tbodyColonias?.addEventListener('input', e => {
        if (e.target.classList.contains('pct-input')) actualizarTabla2();
    });
});
