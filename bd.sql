CREATE DATABASE IF NOT EXISTS concesionario_db
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE concesionario_db;

CREATE TABLE IF NOT EXISTS vehiculos (
    id                INT AUTO_INCREMENT PRIMARY KEY,
    tipo              VARCHAR(50)   NOT NULL,
    marca             VARCHAR(100)  NOT NULL,
    modelo            VARCHAR(100)  NOT NULL,
    precio            DECIMAL(10,2) NOT NULL,
    atributo_especial INT           NOT NULL,
    tipo_motor        VARCHAR(50)   NOT NULL DEFAULT 'Gasolina'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO vehiculos (tipo, marca, modelo, precio, atributo_especial, tipo_motor) VALUES
-- Coches
('Coche',  'Toyota',    'Corolla',      25000.00, 4,     'Gasolina'),
('Coche',  'Tesla',     'Model 3',      48000.00, 4,     'Eléctrico'),
('Coche',  'Toyota',    'RAV4 Hybrid',  36500.00, 5,     'Híbrido'),
('Coche',  'BMW',       '320d',         44000.00, 4,     'Diésel'),
('Coche',  'Ford',      'Mustang GT',   58000.00, 2,     'Gasolina'),
('Coche',  'Volkswagen','Golf GTI',     34000.00, 5,     'Gasolina'),
-- Motos
('Moto',   'Yamaha',    'MT-07',         9500.00, 689,   'Gasolina'),
('Moto',   'Honda',     'CBR600RR',     12000.00, 600,   'Gasolina'),
('Moto',   'Kawasaki',  'Ninja ZX-10R', 17500.00, 998,   'Gasolina'),
('Moto',   'Ducati',    'Monster 937',  14800.00, 937,   'Gasolina'),
-- Camiones
('Camion', 'Mercedes',  'Actros',       85000.00, 18000, 'Diésel'),
('Camion', 'Scania',    'R450',         93000.00, 22000, 'Diésel');
