CREATE DATABASE my_twitter;

CREATE USER 'my_twitter'@'localhost' IDENTIFIED WITH caching_sha2_password BY 'zoNoV0@oQkB2AqTJ';

GRANT ALL PRIVILEGES ON `my_twitter`.* TO 'my_twitter'@'localhost';

FLUSH PRIVILEGES;

use my_twitter;

-- Création de la table des utilisateurs
CREATE TABLE user (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    UNIQUE (username),
    UNIQUE (email)
);

-- Création de la table des tweets
CREATE TABLE tweet (
    tweet_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(user_id) ON DELETE CASCADE ON UPDATE CASCADE
);