-- Todos los campos de registro_diario con nombres en lugar de IDs (tipo de unidad, unidad, chofer, despachador, usuario).
-- Ejecutar sobre la base limpieza_municipio.

USE limpieza_municipio;

SELECT
  r.id_folio,
  r.fecha_orden,
  r.fecha_captura,
  r.turno,
  tu.nombre AS tipo_unidad,
  nu.numero AS numero_unidad,
  r.numero_ruta,
  ch.nombre AS chofer,
  r.cantidad,
  r.comentarios,
  r.num_puches,
  r.km_inicio,
  r.km_final,
  r.total_km,
  r.diesel_inicio,
  r.diesel_final,
  r.diesel_cargado,
  d.nombre AS despachador,
  r.colonia_1, r.colonia_2, r.colonia_3, r.colonia_4, r.colonia_5, r.colonia_6,
  r.colonia_7, r.colonia_8, r.colonia_9, r.colonia_10, r.colonia_11,
  r.pct_colonia_1, r.pct_colonia_2, r.pct_colonia_3, r.pct_colonia_4, r.pct_colonia_5, r.pct_colonia_6,
  r.pct_colonia_7, r.pct_colonia_8, r.pct_colonia_9, r.pct_colonia_10, r.pct_colonia_11,
  r.num_colonias_ruta,
  r.suma_pct_atendida,
  r.pct_efectividad,
  r.habitantes_1, r.habitantes_2, r.habitantes_3, r.habitantes_4, r.habitantes_5, r.habitantes_6,
  r.habitantes_7, r.habitantes_8, r.habitantes_9, r.habitantes_10, r.habitantes_11,
  u.nombre_usuario AS usuario,
  r.id_tipo_unidad,
  r.id_unidad,
  r.id_chofer,
  r.id_despachador,
  r.id_usuario
FROM registro_diario r
INNER JOIN tipo_unidad tu ON r.id_tipo_unidad = tu.id_tipo
INNER JOIN numero_unidad nu ON r.id_unidad = nu.id_unidad
INNER JOIN chofer ch ON r.id_chofer = ch.id_chofer
INNER JOIN despachador d ON r.id_despachador = d.id_despachador
INNER JOIN usuarios u ON r.id_usuario = u.id_usuario
ORDER BY r.id_folio;
