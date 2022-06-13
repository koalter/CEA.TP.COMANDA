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
estado_id INT(11) NOT NULL DEFAULT 1,
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
deleted_at DATETIME NULL,
PRIMARY KEY (id),
FOREIGN KEY fk_mesas_estado (estado_id) REFERENCES estado_mesas (id)
) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE cea_tp_comanda.productos(
id INT(11) AUTO_INCREMENT,
descripcion VARCHAR(255) NOT NULL,
precio DOUBLE NOT NULL,
rol_id INT(11) NOT NULL,
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (id),
FOREIGN KEY fk_productos_rol (rol_id) REFERENCES roles (id)
) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE cea_tp_comanda.usuarios(
id INT(11) AUTO_INCREMENT,
nombre VARCHAR(255) NOT NULL,
rol_id INT(11) NOT NULL,
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
deleted_at DATETIME NULL,
PRIMARY KEY (id),
FOREIGN KEY fk_usuarios_rol (rol_id) REFERENCES roles (id)
) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_general_ci;

CREATE TABLE cea_tp_comanda.pedidos(
id INT(11) AUTO_INCREMENT,
producto_id INT(11) NOT NULL,
cantidad INT(11) NOT NULL DEFAULT 1,
estado_id INT(11) NOT NULL DEFAULT 1,
created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
deleted_at DATETIME NULL,
PRIMARY KEY (id),
FOREIGN KEY fk_pedidos_producto (producto_id) REFERENCES productos (id),
FOREIGN KEY fk_pedidos_estado (estado_id) REFERENCES estado_pedidos (id)
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

INSERT INTO cea_tp_comanda.usuarios (nombre, rol_id)
VALUES ('test_socio', 1),
('test_mozo', 2),
('test_bartender', 5),
('test_cocinero', 3),
('test_cervecero', 4);

INSERT INTO cea_tp_comanda.productos (descripcion, precio, rol_id)
VALUES ('milanesa a caballo', 800, 3),
('hamburguesa de garbanzo', 650, 3),
('corona', 180, 4),
('daikiri', 250, 5)