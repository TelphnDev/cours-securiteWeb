DROP DATABASE IF EXISTS notes_app;
CREATE DATABASE notes_app CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE notes_app;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);

CREATE TABLE notes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    texte VARCHAR(1000) NOT NULL,
    id_utilisateur INT NOT NULL,
    FOREIGN KEY (id_utilisateur) REFERENCES users (id)
);

-- Comptes de test (mots de passe : MonMotDePasse123 et MotDePasseDeBob)
INSERT INTO users (id, email, password_hash) VALUES
    (1, 'alice@example.com', '$2y$10$s6KFh7OKjuhSI2NrmKm97.w0B20OgxDPZLsKDY4zGcjvo1gQSusqW'),
    (2, 'bob@example.com',   '$2y$10$YmksD6sDQHPy/0bd2ll5resdLOUVGd5joVe/wlaj3pfIocJktBmtq');

INSERT INTO notes (texte, id_utilisateur) VALUES
    ('Code du casier : 4821', 1),
    ('Rendre le dossier de projet avant vendredi', 1),
    ('Acheter un câble HDMI', 2);