-- Supprime les tables dans l'ordre inverse des clés étrangères.
DROP TABLE IF EXISTS comments;
DROP TABLE IF EXISTS pledges;
DROP TABLE IF EXISTS rewards;
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- 1. USERS
-- Utilisateurs: créateurs et contributeurs.
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. CATEGORIES
-- Catégories des projets.
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- 3. PROJECTS
-- Campagnes de financement participatif.
-- Relation 1-N: users -> projects
-- Relation 1-N: categories -> projects
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
-- Récompenses offertes par un projet.
-- Relation 1-N: projects -> rewards
CREATE TABLE rewards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    minimum_amount DECIMAL(10,2) NOT NULL,
    quantity INT NULL,

    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

-- 5. PLEDGES
-- Contributions des utilisateurs.
-- Cette table crée la relation N-N entre users et projects.
CREATE TABLE pledges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    project_id INT NOT NULL,
    reward_id INT NULL,
    amount DECIMAL(10,2) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    payment_status VARCHAR(50) NOT NULL DEFAULT 'pending',

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (reward_id) REFERENCES rewards(id) ON DELETE SET NULL
);

-- 6. COMMENTS
-- Commentaires publiés sous les projets.
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    project_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

-- DONNÉES DE TEST
-- Mot de passe de démonstration pour les 3 comptes: demo1234
-- Les mots de passe sont enregistrés sous forme de hash.
INSERT INTO users (name, email, password) VALUES
('Justin', 'justin@test.com', '$2y$12$WVf4qrj6NiSm8DGvY1cGNuXttZ3JOEpLm1aGnHMX39tuqVD125MBm'),
('Jacob', 'jacob@test.com', '$2y$12$i.JI/cfc.hHynxLVW.d/tuzypPaZZmxKVzwXggZovYrUJVzeRe8iu'),
('Kai', 'kai@test.com', '$2y$12$Y6dj3pyA7XCEFg9xRsS4VePzL5bNhtDwgBLy0dP36lw89Txq1K.VW');

INSERT INTO categories (name) VALUES
('Art'), ('Comics'), ('Crafts'), ('Dance'), ('Design'), ('Fashion'), ('Film'), ('Food'),
('Games'), ('Journalism'), ('Music'), ('Photography'), ('Publishing'), ('Technology'), ('Theater');

INSERT INTO projects (user_id, category_id, title, description, goal_amount, current_amount, start_date, end_date, status) VALUES
(2, 14, 'TrailLight Solar Lantern', 'A foldable solar lantern built for long hikes and camping trips. It charges in six hours of sun and lights a tent for a full weekend.', 250.00, 175.00, '2026-10-01', '2026-12-15', 'active'),
(1, 9, 'Pixel Harbour', 'A cozy fishing and exploration game set on a small pixel art island. Help us finish the last chapter and the soundtrack.', 150.00, 60.00, '2026-09-20', '2026-11-30', 'active'),
(3, 11, 'Northern Lights EP', 'Five original songs recorded in a cabin in the Laurentians. Your support covers studio time, mixing and vinyl pressing.', 100.00, 25.00, '2026-10-05', '2026-12-01', 'active'),
(2, 8, 'Maple Street Bakery Book', 'A recipe book with fifty family recipes from a small Joliette bakery, with photos of every step.', 8000.00, 0.00, '2026-10-10', '2027-01-10', 'active'),
(1, 5, 'Modular Desk Organizer', 'A wooden desk organizer made of stackable modules you can arrange any way you like.', 4000.00, 0.00, '2026-08-01', '2026-09-30', 'inactive');

INSERT INTO rewards (project_id, title, description, minimum_amount, quantity) VALUES
(1, 'Early Bird Lantern', 'One TrailLight lantern at a special early price.', 45.00, 98),
(1, 'Camp Pack', 'Two lanterns and a carrying pouch.', 120.00, NULL),
(2, 'Digital Copy', 'A Steam key for the full game on release day.', 20.00, NULL),
(2, 'Collector Edition', 'Game key, art book and your name in the credits.', 75.00, 50),
(3, 'Digital EP', 'All five songs in high quality download.', 10.00, NULL),
(3, 'Signed Vinyl', 'A signed vinyl copy of the EP.', 35.00, 1);

INSERT INTO pledges (user_id, project_id, reward_id, amount, payment_status) VALUES
(1, 1, 1, 50.00, 'completed'),
(3, 1, 2, 125.00, 'completed'),
(2, 2, 3, 20.00, 'completed'),
(3, 2, NULL, 40.00, 'completed'),
(1, 3, 5, 25.00, 'completed');

INSERT INTO comments (user_id, project_id, comment) VALUES
(1, 1, 'Love the idea! Is it waterproof?'),
(2, 1, 'Yes, it is rated IPX6, it can handle heavy rain.'),
(3, 2, 'The pixel art looks amazing, can''t wait to play.');
