CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio_base DECIMAL(10, 2) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE sucursales (
    id_sucursal INT AUTO_INCREMENT PRIMARY KEY,
    nombre_sucursal VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE inventarios (
    id_inventario INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT,
    id_sucursal INT,
    cantidad_disponible INT DEFAULT 0,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto),
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id_sucursal)
) ENGINE=InnoDB;

CREATE TABLE ventas (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,
    id_sucursal INT,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_venta DECIMAL(10, 2),
    FOREIGN KEY (id_sucursal) REFERENCES sucursales(id_sucursal)
) ENGINE=InnoDB;

CREATE TABLE detalle_ventas (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT,
    id_producto INT,
    cantidad INT,
    precio_unitario DECIMAL(10, 2),
    FOREIGN KEY (id_venta) REFERENCES ventas(id_venta),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
) ENGINE=InnoDB;

-- Insertar datos iniciales para que la app no esté vacía
INSERT INTO sucursales (nombre_sucursal) VALUES ('Sucursal Principal');
INSERT INTO productos (nombre, precio_base) VALUES ('Producto de Prueba', 100.00);
INSERT INTO inventarios (id_producto, id_sucursal, cantidad_disponible) VALUES (1, 1, 50);