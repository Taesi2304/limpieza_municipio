DROP DATABASE IF EXISTS limpieza_municipio;
CREATE DATABASE limpieza_municipio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE limpieza_municipio;

-- Usuarios
CREATE TABLE usuarios (
  id_usuario INT PRIMARY KEY AUTO_INCREMENT,
  nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  rol VARCHAR(20) NOT NULL DEFAULT 'Capturista'
) ENGINE=InnoDB;

INSERT INTO usuarios (nombre_usuario, password, rol) VALUES
  ('admin', '123456', 'Admin'),
  ('capturista1', '123456', 'Capturista'),
  ('capturista2', '123456', 'Capturista');
-- Tipos de Unidad
CREATE TABLE tipos_unidad (
  id_tipo INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

INSERT INTO tipos_unidad (nombre) VALUES 
  ('Volteo'), 
  ('Compactadores'), 
  ('Tolva'),
  ('Barredora');

-- Unidades
CREATE TABLE unidades (
  id_unidad INT PRIMARY KEY AUTO_INCREMENT,
  id_tipo INT NOT NULL,
  numero_unidad VARCHAR(20) NOT NULL,
  FOREIGN KEY (id_tipo) REFERENCES tipos_unidad(id_tipo)
) ENGINE=InnoDB;

INSERT INTO unidades (id_tipo, numero_unidad) VALUES
  (1, '66'), (1, '6'),(1, '87'), 
  (2, '504'), (2, '1002'), (2, '1003'),
  (3, '28'), (3, '2304'), (3, '771'),
  (4, '14');

-- Personal
CREATE TABLE personal (
  id_empleado INT PRIMARY KEY AUTO_INCREMENT,
  nombre_completo VARCHAR(100) NOT NULL,
  puesto VARCHAR(20) NOT NULL
) ENGINE=InnoDB;

INSERT INTO personal (nombre_completo, puesto) VALUES
  ('Brandon Velazquez', 'Chofer'),
  ('Jessica Gallegos', 'Chofer'),
  ('Stephany Chávez', 'Chofer'),
  ('Jan Karlo Armendrano', 'Despachador'),
  ('Joel Sanchez', 'Despachador');

-- Rutas (zonas de recolección) con descripción detallada para cada ruta
CREATE TABLE rutas (
  id_ruta INT PRIMARY KEY AUTO_INCREMENT,
  nombre_ruta VARCHAR(50) NOT NULL,
  descripcion VARCHAR(100) DEFAULT NULL
) ENGINE=InnoDB;

INSERT INTO rutas (nombre_ruta, descripcion) VALUES 
  ('1', 'Ruta 1 - Centro'),
  ('2', 'Ruta 2 - Norte'),
  ('3', 'Ruta 3 - Sur'),
  ('4', 'Ruta 4 - Este');

-- Colonias
CREATE TABLE colonias (
  id_colonia INT PRIMARY KEY AUTO_INCREMENT,
  id_ruta INT NOT NULL,
  nombre_colonia VARCHAR(100) NOT NULL,
  habitantes INT NOT NULL DEFAULT 0,
  FOREIGN KEY (id_ruta) REFERENCES rutas(id_ruta)
) ENGINE=InnoDB;

INSERT INTO colonias (id_ruta, nombre_colonia, habitantes) VALUES
  (1,'Col.Matmoros Centro', 1200),
  (1,'Col.Jardín', 800), (1,'Col.Magisterial', 600),
  (2,'Fracc.Bugambilias', 1500), (2,'Fracc.Satelite', 900),
  (2,'Col.Expofiesta Norte',568),(2,'Fracc.Los Presidentes',1000),
  (2,'Col.Puerto Rico',980),(2,'Col.Paseo de las Brisas',1500),(2,'Fracc.Quinta Real',870),
  (2,'Col.Popular',756),
  (3,'Col. Republica Sur', 1100), (3, 'Col. Expofiesta Sur', 700),
  (3,'Col.Puertas Verdes',1680),(3,'Fracc.Paseo del Nogalar',990),
  (3,'Col.Santa Elena',800),(3,'Col.Ladrillera',350),
  (3,'Col.Victoria',1000),(3,'Col.San Ángel',600),
  (3,'Fracc. Hacienda la Cima',870),(3,'Col.Lomas de San Juan',895),
  (3,'Col.Emilo Zapata',850),
  (4, 'Col.Expofiesta Oriente', 500), (4, 'Fracc.Bagdad', 650);

-- Control de Folios
CREATE TABLE control_folios (
  id INT PRIMARY KEY AUTO_INCREMENT,
  ultimo_folio INT NOT NULL DEFAULT 1000
) ENGINE=InnoDB;

INSERT INTO control_folios (ultimo_folio) VALUES (1000);

-- Registro Diario
CREATE TABLE registro_diario (
  folio INT PRIMARY KEY,
  fecha_orden DATE NOT NULL,
  fecha_captura DATETIME NOT NULL,
  turno INT NOT NULL,
  id_ruta INT NOT NULL,
  id_despachador INT NOT NULL,
  id_chofer INT NOT NULL,
  id_tipo_unidad INT NOT NULL,
  id_unidad INT NOT NULL,
  cantidad_kg DECIMAL(10,2) NOT NULL,
  cantidad_puches INT NOT NULL,
  km_salida DECIMAL(10,2) NOT NULL,
  km_entrada DECIMAL(10,2) NOT NULL,
  total_km DECIMAL(10,2) NOT NULL,
  diesel_inicio DECIMAL(10,2) NOT NULL,
  diesel_final DECIMAL(10,2) NOT NULL,
  diesel_cargado DECIMAL(10,2) NOT NULL DEFAULT 0,
  total_diesel DECIMAL(10,2) NOT NULL,
  usuario_captura VARCHAR(50) NOT NULL,
  FOREIGN KEY (id_ruta) REFERENCES rutas(id_ruta),
  FOREIGN KEY (id_despachador) REFERENCES personal(id_empleado),
  FOREIGN KEY (id_chofer) REFERENCES personal(id_empleado),
  FOREIGN KEY (id_tipo_unidad) REFERENCES tipos_unidad(id_tipo),
  FOREIGN KEY (id_unidad) REFERENCES unidades(id_unidad)
) ENGINE=InnoDB;

-- Detalle Colonias
CREATE TABLE registro_detalle_colonias (
  id_detalle INT PRIMARY KEY AUTO_INCREMENT,
  folio INT NOT NULL,
  id_colonia INT NOT NULL,
  porcentaje_recolectado DECIMAL(5,2) NOT NULL DEFAULT 0,
  FOREIGN KEY (folio) REFERENCES registro_diario(folio) ON DELETE CASCADE,
  FOREIGN KEY (id_colonia) REFERENCES colonias(id_colonia)
) ENGINE=InnoDB;
