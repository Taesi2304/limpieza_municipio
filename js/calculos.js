// Cálculos automáticos
function actualizarTabla2() {
    const inputs = document.querySelectorAll('.pct-input');
    if (!inputs.length) return;

    let suma = 0;
    inputs.forEach(i => { suma += parseFloat(i.value) || 0; });

    const efectividad = (suma / inputs.length).toFixed(1);
    const ahora = new Date();

    const elFecha = document.getElementById('tabla2_fecha');
    const elHora = document.getElementById('tabla2_hora');
    if (elFecha && !elFecha.dataset.fromServer) {
        elFecha.textContent = ahora.toLocaleDateString('es-MX');
    }
    if (elHora && !elHora.dataset.fromServer) {
        elHora.textContent = ahora.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });
    }
    const elSuma = document.getElementById('tabla2_suma');
    const elCol = document.getElementById('tabla2_colonias');
    const elEf = document.getElementById('tabla2_efectividad');
    if (elSuma) elSuma.textContent = suma.toFixed(1);
    if (elCol) elCol.textContent = inputs.length;
    if (elEf) elEf.textContent = efectividad + '%';
}

document.addEventListener('DOMContentLoaded', function() {
    // KM recorridos = km_inicio - km_final (odómetro al inicio y al terminar)
    function calcularKM() {
        const inicio = parseFloat(document.getElementById('km_inicio')?.value) || 0;
        const final = parseFloat(document.getElementById('km_final')?.value) || 0;
        const total = document.getElementById('total_km');
        if (total) total.value = Math.max(0, inicio - final).toFixed(2);
    }

    document.getElementById('km_inicio')?.addEventListener('input', calcularKM);
    document.getElementById('km_final')?.addEventListener('input', calcularKM);
    
    // Total Diesel
    function calcularDiesel() {
        const inicio = parseFloat(document.getElementById('diesel_inicio')?.value) || 0;
        const final = parseFloat(document.getElementById('diesel_final')?.value) || 0;
        const cargado = parseFloat(document.getElementById('diesel_cargado')?.value) || 0;
        const total = document.getElementById('total_diesel');
        if (total) total.value = Math.max(0, (inicio - final) + cargado).toFixed(2);
    }
    
    document.getElementById('diesel_inicio')?.addEventListener('input', calcularDiesel);
    document.getElementById('diesel_final')?.addEventListener('input', calcularDiesel);
    document.getElementById('diesel_cargado')?.addEventListener('input', calcularDiesel);
});
