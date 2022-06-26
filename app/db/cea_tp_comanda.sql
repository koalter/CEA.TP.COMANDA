-- CREATE DATABASE
CREATE DATABASE cea_tp_comanda;

-- CREATE tables
CREATE TABLE cea_tp_comanda.roles(
id INT(11) AUTO_INCREMENT PRIMARY KEY,
nombre VARCHAR(100) NOT NULL,
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE cea_tp_comanda.estado_mesas(
id INT(11) AUTO_INCREMENT PRIMARY KEY,
descripcion VARCHAR(255) NOT NULL,
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE cea_tp_comanda.estado_pedidos(
id INT(11) AUTO_INCREMENT PRIMARY KEY,
descripcion VARCHAR(255) NOT NULL,
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE cea_tp_comanda.mesas(
id INT(11) AUTO_INCREMENT,
cliente VARCHAR(255) NOT NULL,
codigo VARCHAR(5) NULL,
foto VARCHAR(255) NULL,
estado_id INT(11) NOT NULL DEFAULT 1,
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
deleted_at DATETIME NULL,
PRIMARY KEY (id),
FOREIGN KEY fk_mesas_estado (estado_id) REFERENCES estado_mesas (id),
UNIQUE (codigo)
) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE cea_tp_comanda.productos(
id INT(11) AUTO_INCREMENT,
descripcion VARCHAR(255) NOT NULL,
precio DOUBLE NOT NULL,
rol_id INT(11) NOT NULL,
tiempo_preparacion INT(11) NOT NULL,
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (id),
FOREIGN KEY fk_productos_rol (rol_id) REFERENCES roles (id)
) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE cea_tp_comanda.usuarios(
id INT(11) AUTO_INCREMENT,
nombre VARCHAR(255) NOT NULL,
clave VARCHAR(255) NOT NULL,
rol_id INT(11) NOT NULL,
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
deleted_at DATETIME NULL,
PRIMARY KEY (id),
FOREIGN KEY fk_usuarios_rol (rol_id) REFERENCES roles (id)
) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE cea_tp_comanda.pedidos(
id INT(11) AUTO_INCREMENT,
cantidad INT(11) NOT NULL DEFAULT 1,
tiempo_preparacion DATETIME NULL,
producto_id INT(11) NOT NULL,
mesa_id INT(11) NOT NULL,
estado_id INT(11) NOT NULL DEFAULT 1,
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
deleted_at DATETIME NULL,
PRIMARY KEY (id),
FOREIGN KEY fk_pedidos_producto (producto_id) REFERENCES productos (id),
FOREIGN KEY fk_pedidos_estado (estado_id) REFERENCES estado_pedidos (id),
FOREIGN KEY fk_pedidos_mesa (mesa_id) REFERENCES mesas (id)
) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE cea_tp_comanda.encuestas(
id INT NOT NULL AUTO_INCREMENT, 
puntuacion_mesa TINYINT NOT NULL, 
puntuacion_restaurante TINYINT NOT NULL, 
puntuacion_mozo TINYINT NOT NULL, 
puntuacion_cocinero TINYINT NOT NULL, 
opinion VARCHAR(66) NOT NULL,
promedio DECIMAL(4, 2) NOT NULL, 
mesa_id INT NOT NULL,
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
deleted_at DATETIME NULL, 
PRIMARY KEY (id),
FOREIGN KEY fk_encuestas_mesa (mesa_id) REFERENCES mesas (id)
) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

-- INSERT data
INSERT INTO cea_tp_comanda.roles (nombre)
VALUES ('socio'),
('mozo'),
('cocinero'),
('cervecero'),
('bartender');

INSERT INTO cea_tp_comanda.estado_mesas (descripcion)
VALUES ('con cliente esperando pedido'),
('con cliente comiendo'),
('con cliente pagando'),
('cerrada');

INSERT INTO cea_tp_comanda.estado_pedidos (descripcion)
VALUES ('pendiente'),
('en preparacion'),
('listo para servir'),
('servido');

INSERT INTO cea_tp_comanda.usuarios (nombre, rol_id, clave) -- clave = 'password'
VALUES ('test_socio', 1, '$2y$10$M2LLDW5MmiuqGGmQbqpYYuSngtnqAiAiHmPwnbkzVty1AA/tzZIfW'),
('test_mozo', 2, '$2y$10$tn6hASWCJlCEKr.DgoY0qOPvQnNErRhl4em0vEcjqjTIUClD1ll0W'),
('test_bartender', 5, '$2y$10$w5LTksgi5sjZoZvUgx9EP.DRpcowGZ4hbGTi.dC50PDqcbQwN5GL.'),
('test_cocinero', 3, '$2y$10$3XffNkVdPvUbRBMhe6uGT.JGV3U6xPpW0/boEHUue4YvtiVdqD5YS'),
('test_cervecero', 4, '$2y$10$vPavHYgRJoh2RTliazeFheLcwEWiqvY6iV/5fVkasVhI2dGNK1fbS');

INSERT INTO cea_tp_comanda.productos (descripcion, precio, rol_id, tiempo_preparacion)
VALUES ('milanesa a caballo', 800, 3, 10),
('hamburguesa de garbanzo', 650, 3, 8),
('corona', 180, 4, 3),
('daikiri', 250, 5, 3);
