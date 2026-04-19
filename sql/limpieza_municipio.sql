DROP DATABASE IF EXISTS limpieza_municipio;
CREATE DATABASE limpieza_municipio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE limpieza_municipio;

-- ---------------------------------------------------------------------------
-- 1. usuarios
-- ---------------------------------------------------------------------------
CREATE TABLE usuarios (
  id_usuario INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre_usuario VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  rol VARCHAR(20) NOT NULL DEFAULT 'Capturista',
  PRIMARY KEY (id_usuario),
  UNIQUE KEY uk_nombre_usuario (nombre_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO usuarios (nombre_usuario, password, rol) VALUES
  ('admin', '123456', 'Admin'),
  ('capturista1', '123456', 'Capturista'),
  ('capturista2', '123456', 'Capturista');

-- ---------------------------------------------------------------------------
-- 2. tipo_unidad
-- ---------------------------------------------------------------------------
CREATE TABLE tipo_unidad (
  id_tipo INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(50) NOT NULL,
  PRIMARY KEY (id_tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO tipo_unidad (nombre) VALUES
  ('Volteo'),
  ('Compactadores'),
  ('Tolva'),
  ('Barredora');

-- ---------------------------------------------------------------------------
-- 3. numero_unidad (unidades numeradas por tipo)
-- ---------------------------------------------------------------------------
CREATE TABLE numero_unidad (
  id_unidad INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_tipo INT UNSIGNED NOT NULL,
  numero VARCHAR(20) NOT NULL,
  PRIMARY KEY (id_unidad),
  KEY fk_numero_unidad_tipo (id_tipo),
  CONSTRAINT fk_numero_unidad_tipo FOREIGN KEY (id_tipo) REFERENCES tipo_unidad (id_tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO numero_unidad (id_tipo, numero) VALUES
  (1, '66'), (1, '6'), (1, '87'),
  (2, '504'), (2, '1002'), (2, '1003'),
  (3, '28'), (3, '2304'), (3, '771'),
  (4, '14');

-- ---------------------------------------------------------------------------
-- 4. chofer
-- ---------------------------------------------------------------------------
CREATE TABLE chofer (
  id_chofer INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  PRIMARY KEY (id_chofer)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO chofer (nombre) VALUES
  ('Brandon Velazquez'),
  ('Jessica Gallegos'),
  ('Stephany Chávez');

-- ---------------------------------------------------------------------------
-- 5. despachador
-- ---------------------------------------------------------------------------
CREATE TABLE despachador (
  id_despachador INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  PRIMARY KEY (id_despachador)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO despachador (nombre) VALUES
  ('Jan Karlo Armendrano'),
  ('Joel Sanchez');

-- ---------------------------------------------------------------------------
-- 6. folio (número de folio que identifica cada registro diario)
-- ---------------------------------------------------------------------------
CREATE TABLE folio (
  id_folio INT UNSIGNED NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (id_folio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- 7. rutas (catálogo: colonias por número de ruta 1, 2 o 3)
-- ---------------------------------------------------------------------------
CREATE TABLE rutas (
  id_registro INT UNSIGNED NOT NULL AUTO_INCREMENT,
  id_ruta TINYINT UNSIGNED NOT NULL,
  colonia VARCHAR(120) NOT NULL,
  habitantes DECIMAL(12,2) NOT NULL DEFAULT 0,
  PRIMARY KEY (id_registro),
  KEY idx_rutas_id_ruta (id_ruta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ruta 1: 3 colonias | Ruta 2: 8 colonias | Ruta 3: 11 colonias
INSERT INTO rutas (id_ruta, colonia, habitantes) VALUES
  (1, 'Col.Matmoros Centro', 1200),
  (1, 'Col.Jardín', 800),
  (1, 'Col.Magisterial', 600),
  (2, 'Fracc.Bugambilias', 1500),
  (2, 'Fracc.Satelite', 900),
  (2, 'Col.Expofiesta Norte', 568),
  (2, 'Fracc.Los Presidentes', 1000),
  (2, 'Col.Puerto Rico', 980),
  (2, 'Col.Paseo de las Brisas', 1500),
  (2, 'Fracc.Quinta Real', 870),
  (2, 'Col.Popular', 756),
  (3, 'Col. Republica Sur', 1100),
  (3, 'Col. Expofiesta Sur', 700),
  (3, 'Col.Puertas Verdes', 1680),
  (3, 'Fracc.Paseo del Nogalar', 990),
  (3, 'Col.Santa Elena', 800),
  (3, 'Col.Ladrillera', 350),
  (3, 'Col.Victoria', 1000),
  (3, 'Col.San Ángel', 600),
  (3, 'Fracc. Hacienda la Cima', 870),
  (3, 'Col.Lomas de San Juan', 895),
  (3, 'Col.Emilo Zapata', 850);

-- ---------------------------------------------------------------------------
-- 8. registro_diario
-- ---------------------------------------------------------------------------
CREATE TABLE registro_diario (
  id_folio INT UNSIGNED NOT NULL,
  fecha_orden DATE NOT NULL,
  fecha_captura DATETIME NOT NULL,
  turno TINYINT UNSIGNED NOT NULL,
  id_tipo_unidad INT UNSIGNED NOT NULL,
  id_unidad INT UNSIGNED NOT NULL,
  numero_ruta TINYINT UNSIGNED NOT NULL,
  id_chofer INT UNSIGNED NOT NULL,
  cantidad DECIMAL(10,2) NOT NULL DEFAULT 0,
  comentarios TEXT,
  num_puches INT NOT NULL DEFAULT 0,
  km_inicio DECIMAL(10,2) NOT NULL DEFAULT 0,
  km_final DECIMAL(10,2) NOT NULL DEFAULT 0,
  total_km DECIMAL(10,2) NOT NULL DEFAULT 0,
  diesel_inicio DECIMAL(10,2) NOT NULL DEFAULT 0,
  diesel_final DECIMAL(10,2) NOT NULL DEFAULT 0,
  diesel_cargado DECIMAL(10,2) NOT NULL DEFAULT 0,
  id_despachador INT UNSIGNED NOT NULL,
  colonia_1 VARCHAR(120) DEFAULT NULL,
  colonia_2 VARCHAR(120) DEFAULT NULL,
  colonia_3 VARCHAR(120) DEFAULT NULL,
  colonia_4 VARCHAR(120) DEFAULT NULL,
  colonia_5 VARCHAR(120) DEFAULT NULL,
  colonia_6 VARCHAR(120) DEFAULT NULL,
  colonia_7 VARCHAR(120) DEFAULT NULL,
  colonia_8 VARCHAR(120) DEFAULT NULL,
  colonia_9 VARCHAR(120) DEFAULT NULL,
  colonia_10 VARCHAR(120) DEFAULT NULL,
  colonia_11 VARCHAR(120) DEFAULT NULL,
  pct_colonia_1 DECIMAL(5,2) DEFAULT NULL,
  pct_colonia_2 DECIMAL(5,2) DEFAULT NULL,
  pct_colonia_3 DECIMAL(5,2) DEFAULT NULL,
  pct_colonia_4 DECIMAL(5,2) DEFAULT NULL,
  pct_colonia_5 DECIMAL(5,2) DEFAULT NULL,
  pct_colonia_6 DECIMAL(5,2) DEFAULT NULL,
  pct_colonia_7 DECIMAL(5,2) DEFAULT NULL,
  pct_colonia_8 DECIMAL(5,2) DEFAULT NULL,
  pct_colonia_9 DECIMAL(5,2) DEFAULT NULL,
  pct_colonia_10 DECIMAL(5,2) DEFAULT NULL,
  pct_colonia_11 DECIMAL(5,2) DEFAULT NULL,
  num_colonias_ruta INT UNSIGNED NOT NULL DEFAULT 0,
  suma_pct_atendida DECIMAL(8,2) NOT NULL DEFAULT 0,
  pct_efectividad DECIMAL(5,2) NOT NULL DEFAULT 0,
  habitantes_1 DECIMAL(12,2) DEFAULT NULL,
  habitantes_2 DECIMAL(12,2) DEFAULT NULL,
  habitantes_3 DECIMAL(12,2) DEFAULT NULL,
  habitantes_4 DECIMAL(12,2) DEFAULT NULL,
  habitantes_5 DECIMAL(12,2) DEFAULT NULL,
  habitantes_6 DECIMAL(12,2) DEFAULT NULL,
  habitantes_7 DECIMAL(12,2) DEFAULT NULL,
  habitantes_8 DECIMAL(12,2) DEFAULT NULL,
  habitantes_9 DECIMAL(12,2) DEFAULT NULL,
  habitantes_10 DECIMAL(12,2) DEFAULT NULL,
  habitantes_11 DECIMAL(12,2) DEFAULT NULL,
  id_usuario INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_folio),
  CONSTRAINT fk_registro_folio FOREIGN KEY (id_folio) REFERENCES folio (id_folio),
  CONSTRAINT fk_registro_tipo_unidad FOREIGN KEY (id_tipo_unidad) REFERENCES tipo_unidad (id_tipo),
  CONSTRAINT fk_registro_numero_unidad FOREIGN KEY (id_unidad) REFERENCES numero_unidad (id_unidad),
  CONSTRAINT fk_registro_chofer FOREIGN KEY (id_chofer) REFERENCES chofer (id_chofer),
  CONSTRAINT fk_registro_despachador FOREIGN KEY (id_despachador) REFERENCES despachador (id_despachador),
  CONSTRAINT fk_registro_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
