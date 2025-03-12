CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cuit VARCHAR(11) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    direccion VARCHAR(255),
    condicion_fiscal VARCHAR(50)
);

CREATE TABLE comprobantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    tipo VARCHAR(50) NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    fecha_emision DATETIME DEFAULT CURRENT_TIMESTAMP,
    cae VARCHAR(50),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

CREATE TABLE proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cuit VARCHAR(11) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    direccion VARCHAR(255),
    condicion_fiscal VARCHAR(50)
);

CREATE TABLE compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proveedor_id INT NOT NULL,
    monto DECIMAL(10, 2) NOT NULL,
    fecha_compra DATE NOT NULL,
    descripcion TEXT,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id)
);

CREATE USER 'usuario'@'localhost' IDENTIFIED BY 'contraseña';
GRANT ALL PRIVILEGES ON modulo_contable.* TO 'usuario'@'localhost';
FLUSH PRIVILEGES;