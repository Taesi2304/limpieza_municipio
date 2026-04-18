// Cálculos automáticos
function actualizarTabla2() {
    const inputs = document.querySelectorAll('.pct-input');
    if (!inputs.length) return;

    let suma = 0;
    inputs.forEach(i => { suma += parseFloat(i.value) || 0; });

    const efectividad = (suma / inputs.length).toFixed(1);
    const ahora = new Date();

    document.getElementById('tabla2_fecha').textContent = ahora.toLocaleDateString('es-MX');
    document.getElementById('tabla2_hora').textContent  = ahora.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });
    document.getElementById('tabla2_suma').textContent       = suma.toFixed(1);
    document.getElementById('tabla2_colonias').textContent   = inputs.length;
    document.getElementById('tabla2_efectividad').textContent = efectividad + '%';
}

document.addEventListener('DOMContentLoaded', function() {
    // KM Recorridos
    function calcularKM() {
        const salida = parseFloat(document.getElementById('km_salida')?.value) || 0;
        const entrada = parseFloat(document.getElementById('km_entrada')?.value) || 0;
        const total = document.getElementById('total_km');
        if (total) total.value = Math.max(0, entrada - salida).toFixed(2);
    }
    
    document.getElementById('km_salida')?.addEventListener('input', calcularKM);
    document.getElementById('km_entrada')?.addEventListener('input', calcularKM);
    
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
