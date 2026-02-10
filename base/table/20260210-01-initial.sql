-- =========================
-- Takalo-takalo (MySQL/MariaDB)
-- Création + données de test
-- =========================

-- Use your database
-- USE your_database_name;

-- Disable foreign key checks to allow dropping tables in any order
SET FOREIGN_KEY_CHECKS = 0;

-- (Optionnel) nettoyer si tu relances le script
DROP TABLE IF EXISTS echange;
DROP TABLE IF EXISTS proposition;
DROP TABLE IF EXISTS histo_propietaire;
DROP TABLE IF EXISTS objet_image;
DROP TABLE IF EXISTS objet;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- =========================
-- 1) TABLES
-- =========================

CREATE TABLE users (
    id              BIGINT PRIMARY KEY AUTO_INCREMENT,
    name            VARCHAR(120) NOT NULL,
    role            VARCHAR(20)  NOT NULL DEFAULT 'USER',
    created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    password_hash   VARCHAR(255) NOT NULL,
    
    CONSTRAINT users_role_chk CHECK (role IN ('ADMIN','USER'))
);

CREATE TABLE categories (
    id          BIGINT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(80) NOT NULL UNIQUE,
    created_at  TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE objet (
    id              BIGINT PRIMARY KEY AUTO_INCREMENT,
    title           VARCHAR(160) NOT NULL,
    description     TEXT,
    estimated_value DECIMAL(12,2) NOT NULL DEFAULT 0,
    owner_user_id   BIGINT NOT NULL,
    category_id     BIGINT NOT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT objet_owner_fk    FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT objet_category_fk FOREIGN KEY (category_id)   REFERENCES categories(id) ON DELETE RESTRICT
);

CREATE TABLE objet_image (
    id          BIGINT PRIMARY KEY AUTO_INCREMENT,
    objet_id    BIGINT NOT NULL,
    path        VARCHAR(255) NOT NULL,
    is_main     BOOLEAN NOT NULL DEFAULT FALSE,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT objet_image_objet_fk FOREIGN KEY (objet_id) REFERENCES objet(id) ON DELETE CASCADE
);

-- Pour éviter plusieurs images "principales" sur le même objet (MySQL/MariaDB)
-- Note: MySQL n'a pas de partial unique indexes comme PostgreSQL, on utilise un trigger ou une approche différente
-- Alternative: Utiliser une clé composite unique avec une colonne virtuelle
CREATE UNIQUE INDEX objet_image_one_main_per_objet ON objet_image (objet_id, is_main);

CREATE TABLE proposition (
    id              BIGINT PRIMARY KEY AUTO_INCREMENT,
    requester_id    BIGINT NOT NULL,      -- celui qui propose
    owner_id        BIGINT NOT NULL,      -- propriétaire de l'objet voulu
    wanted_id       BIGINT NOT NULL,      -- objet voulu (appartient à owner_id)
    offered_id      BIGINT NOT NULL,      -- objet proposé (appartient à requester_id)
    message         TEXT,
    status          VARCHAR(20) NOT NULL DEFAULT 'PENDING',
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    responded_at    TIMESTAMP NULL DEFAULT NULL,
    
    CONSTRAINT proposition_requester_fk FOREIGN KEY (requester_id) REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT proposition_owner_fk     FOREIGN KEY (owner_id)     REFERENCES users(id) ON DELETE RESTRICT,
    CONSTRAINT proposition_wanted_fk    FOREIGN KEY (wanted_id)    REFERENCES objet(id) ON DELETE RESTRICT,
    CONSTRAINT proposition_offered_fk   FOREIGN KEY (offered_id)   REFERENCES objet(id) ON DELETE RESTRICT,
    
    CONSTRAINT proposition_status_chk CHECK (status IN ('PENDING','ACCEPTED','REJECTED','CANCELLED')),
    CONSTRAINT proposition_objects_diff_chk CHECK (wanted_id <> offered_id)
);

CREATE TABLE echange (
    id              BIGINT PRIMARY KEY AUTO_INCREMENT,
    proposition_id  BIGINT NOT NULL UNIQUE,
    traded_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT echange_prop_fk FOREIGN KEY (proposition_id) REFERENCES proposition(id) ON DELETE CASCADE
);

-- Historique propriétaire (selon ta structure : seulement start_at)
CREATE TABLE histo_propietaire (
    id          BIGINT PRIMARY KEY AUTO_INCREMENT,
    objet_id    BIGINT NOT NULL,
    user_id     BIGINT NOT NULL,
    start_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT histo_objet_fk FOREIGN KEY (objet_id) REFERENCES objet(id) ON DELETE CASCADE,
    CONSTRAINT histo_user_fk  FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE RESTRICT
);

-- Index utiles
CREATE INDEX idx_objet_owner      ON objet(owner_user_id);
CREATE INDEX idx_objet_category   ON objet(category_id);
CREATE INDEX idx_prop_status      ON proposition(status);
CREATE INDEX idx_prop_owner       ON proposition(owner_id);
CREATE INDEX idx_prop_requester   ON proposition(requester_id);

-- =========================
-- 2) DONNÉES DE TEST
-- =========================

-- Users (password_hash = placeholders)
INSERT INTO users (name, role, password_hash) VALUES
('Admin', 'ADMIN', '$2y$10$adminhash_placeholder___________________________'),
('Aina',  'USER',  '$2y$10$ainahash_placeholder____________________________'),
('Koto',  'USER',  '$2y$10$kotohash_placeholder____________________________'),
('Mina',  'USER',  '$2y$10$minahash_placeholder____________________________');

-- Categories
INSERT INTO categories (name) VALUES
('Vêtements'),
('Livres'),
('DVD'),
('Électronique');

-- Objets (owner_user_id: Aina=2, Koto=3, Mina=4)
INSERT INTO objet (title, description, estimated_value, owner_user_id, category_id) VALUES
('Veste en jean', 'Taille M, bon état', 35000, 2, 1),
('Roman: Le Petit Prince', 'Édition poche', 15000, 2, 2),
('DVD Inception', 'Boîte d''origine', 20000, 3, 3),
('Casque audio', 'Bluetooth, autonomie 10h', 60000, 3, 4),
('T-shirt noir', 'Taille L, neuf', 20000, 4, 1),
('Livre Python débutant', 'Livre + exercices', 30000, 4, 2);

-- Images (mettre une image principale)
INSERT INTO objet_image (objet_id, path, is_main) VALUES
(1, '/uploads/items/1/main.jpg', TRUE),
(1, '/uploads/items/1/2.jpg', FALSE),
(2, '/uploads/items/2/main.jpg', TRUE),
(3, '/uploads/items/3/main.jpg', TRUE),
(4, '/uploads/items/4/main.jpg', TRUE),
(5, '/uploads/items/5/main.jpg', TRUE),
(6, '/uploads/items/6/main.jpg', TRUE);

-- Historique propriétaires (initial)
INSERT INTO histo_propietaire (objet_id, user_id, start_at) VALUES
(1, 2, NOW()), (2, 2, NOW()),
(3, 3, NOW()), (4, 3, NOW()),
(5, 4, NOW()), (6, 4, NOW());

-- Propositions :
-- Aina (2) propose son objet #2 (Petit Prince) contre le casque #4 de Koto (3)
INSERT INTO proposition (requester_id, owner_id, wanted_id, offered_id, message, status)
VALUES (2, 3, 4, 2, 'Salut, échange mon livre contre ton casque ?', 'PENDING');

-- Mina (4) propose son objet #6 contre la veste #1 de Aina (2) => acceptée
INSERT INTO proposition (requester_id, owner_id, wanted_id, offered_id, message, status, responded_at)
VALUES (4, 2, 1, 6, 'Je te propose mon livre Python contre ta veste.', 'ACCEPTED', NOW());

-- Échange créé pour la proposition acceptée (id=2 si c'est le 2e insert, mais on le récupère proprement)
INSERT INTO echange (proposition_id, traded_at)
SELECT id, NOW()
FROM proposition
WHERE status = 'ACCEPTED'
ORDER BY id DESC
LIMIT 1;

-- =========================
-- Vérifs rapides
-- =========================
-- SELECT * FROM users;
-- SELECT * FROM categories;
-- SELECT * FROM objet;
-- SELECT * FROM proposition;
-- SELECT * FROM echange;