
-- Nettoyage et insertion de données de test pour EcoRide

-- Création de la table utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(100) NOT NULL,
    photo VARCHAR(255)
);

-- Création de la table vehicules
CREATE TABLE IF NOT EXISTS vehicules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marque VARCHAR(100) NOT NULL,
    modele VARCHAR(100) NOT NULL,
    eco BOOLEAN DEFAULT FALSE
);

-- Création de la table trajets
CREATE TABLE IF NOT EXISTS trajets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conducteur_id INT NOT NULL,
    vehicule_id INT NOT NULL,
    adresse_depart VARCHAR(255) NOT NULL,
    adresse_arrivee VARCHAR(255) NOT NULL,
    date_depart DATETIME NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    statut VARCHAR(50) DEFAULT 'à venir',
    places_disponibles INT DEFAULT 1,
    FOREIGN KEY (conducteur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (vehicule_id) REFERENCES vehicules(id)
);

-- Insertion d'utilisateurs
INSERT INTO utilisateurs (pseudo, photo) VALUES
('JeanCovoit', 'jean.jpg'),
('SophieVoyage', 'sophie.jpg');

-- Insertion de véhicules
INSERT INTO vehicules (marque, modele, eco) VALUES
('Tesla', 'Model 3', 1),
('Peugeot', '208', 0);

-- Insertion de trajets
INSERT INTO trajets (conducteur_id, vehicule_id, adresse_depart, adresse_arrivee, date_depart, prix, statut, places_disponibles) VALUES
(1, 1, 'Paris', 'Lyon', '2025-05-15 08:30:00', 25.00, 'à venir', 3),
(2, 2, 'Marseille', 'Nice', '2025-05-16 10:00:00', 15.00, 'à venir', 0);
