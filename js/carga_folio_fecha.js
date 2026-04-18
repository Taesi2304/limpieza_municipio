        // Cargar folio provisional
    fetch('api/obtener_folio_provisional.php')
            .then(r => r.json())
            .then(data => {
        document.getElementById('folio_display').textContent = data.folio;
    document.getElementById('folio_hidden').value = data.folio;
            });

    // Auto-completar fechas
    document.querySelector('[name="fecha_orden"]').valueAsDate = new Date();
    const now = new Date();
    const datetime = now.getFullYear() + '-' +
    String(now.getMonth() + 1).padStart(2, '0') + '-' +
    String(now.getDate()).padStart(2, '0') + 'T' +
    String(now.getHours()).padStart(2, '0') + ':' +
    String(now.getMinutes()).padStart(2, '0');
    document.getElementById('fecha_captura').value = datetime;
