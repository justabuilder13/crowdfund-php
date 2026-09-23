-- TP1 - Base de données Crowdfunding
-- Le script recrée complètement la base de données.

-- 1. USERS
-- Utilisateurs / créateurs / contributeurs.
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. CATEGORIES
-- Catégories disponibles pour les projets.
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- 3. PROJECTS
-- Campagnes de financement participatif.
-- Relation 1-N : users -> projects
-- Relation 1-N : categories -> projects
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    goal_amount DECIMAL(10,2) NOT NULL,
    current_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'active',
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- 4. REWARDS
-- Récompenses offertes pour un projet.
-- Relation 1-N : projects -> rewards
CREATE TABLE rewards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    minimum_amount DECIMAL(10,2) NOT NULL,
    quantity INT NULL,

    FOREIGN KEY (project_id)
        REFERENCES projects(id)
        ON DELETE CASCADE
);

-- 5. PLEDGES
-- Contributions financières des utilisateurs.
-- Cette table crée la relation N-N entre users et projects.
CREATE TABLE pledges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    project_id INT NOT NULL,
    reward_id INT NULL,
    amount DECIMAL(10,2) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    payment_status VARCHAR(50) NOT NULL DEFAULT 'pending',

    FOREIGN KEY (user_id)
        REFERENCES users(id),

    FOREIGN KEY (project_id)
        REFERENCES projects(id)
        ON DELETE CASCADE,

    FOREIGN KEY (reward_id)
        REFERENCES rewards(id)
        ON DELETE SET NULL
);

-- 6. COMMENTS
-- Commentaires publiés sous les projets.
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    project_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
        REFERENCES users(id),

    FOREIGN KEY (project_id)
        REFERENCES projects(id)
        ON DELETE CASCADE
);

-- DONNÉES DE TEST
-- Mot de passe de démonstration pour les 3 comptes : demo1234
-- Les mots de passe sont enregistrés sous forme de hash.
INSERT INTO users (name, email, password)
VALUES
('Justin', 'justin@test.com', '$2y$12$WVf4qrj6NiSm8DGvY1cGNuXttZ3JOEpLm1aGnHMX39tuqVD125MBm'),
('Jacob', 'jacob@test.com', '$2y$12$i.JI/cfc.hHynxLVW.d/tuzypPaZZmxKVzwXggZovYrUJVzeRe8iu'),
('Kai', 'kai@test.com', '$2y$12$Y6dj3pyA7XCEFg9xRsS4VePzL5bNhtDwgBLy0dP36lw89Txq1K.VW');

INSERT INTO categories (name)
VALUES
('Art'),
('Comics'),
('Crafts'),
('Dance'),
('Design'),
('Fashion'),
('Film'),
('Food'),
('Games'),
('Journalism'),
('Music'),
('Photography'),
('Publishing'),
('Technology'),
('Theater');
