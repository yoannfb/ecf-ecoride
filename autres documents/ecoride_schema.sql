
-- Création des tables pour l'application EcoRide

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pseudo VARCHAR(100) NOT NULL,
    photo VARCHAR(255)
);

CREATE TABLE vehicules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marque VARCHAR(100) NOT NULL,
    modele VARCHAR(100) NOT NULL,
    eco BOOLEAN DEFAULT FALSE
);

CREATE TABLE trajets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    adresse_depart VARCHAR(255) NOT NULL,
    adresse_arrivee VARCHAR(255) NOT NULL,
    date_depart DATETIME NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    statut VARCHAR(50) DEFAULT 'à venir',
    conducteur_id INT,
    vehicule_id INT,
    places_disponibles INT DEFAULT 1,
    FOREIGN KEY (conducteur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (vehicule_id) REFERENCES vehicules(id)
);
