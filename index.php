<?php

require_once 'vendor/autoload.php'; // Assurez-vous d'installer Faker via Composer

use Faker\Factory;
use Faker\Provider\Lorem;

// Paramètres de connexion à la base de données (à ajuster en fonction de votre configuration)
$dsn = 'mysql:host=localhost;dbname=my_twitter';
$user = 'my_twitter';
$password = 'zoNoV0@oQkB2AqTJ';

// Fonction pour insérer des utilisateurs factices dans la table "user"
function insertFakeUsers($pdo, $faker, $numUsers)
{
    $mailProviders = ['gmail.com', 'hotmail.fr', 'yahoo.fr'];

    $countProviders = count($mailProviders);
    $i = 0;
    while ($i < $numUsers) {
        $data = [];
        for ($j = 0; $j < 100; $j++) {
            $username = 'user_' . $i;
            $email = $username . '@' . ($mailProviders[$faker->numberBetween(0, $countProviders - 1)]);
            $data[] = [$username, $email];
            $i++;
            if ($i >= $numUsers) {
                break;
            }
        }
        $sql = buildInsertStatement("user (username, email)", $data);
        $stmt = $pdo->prepare($sql);
        // execute with all values from $data
        $stmt->execute(array_merge(...$data));
    }

    echo "Utilisateurs insérés avec succès!\n";
}

function buildInsertStatement($into, $data)
{
    // create the ?,? sequence for a single row
    $values = str_repeat('?,', count($data[0]) - 1) . '?';
    // construct the entire query
    $sql = "INSERT INTO $into VALUES " .
        // repeat the (?,?) sequence for each row
        str_repeat("($values),", count($data) - 1) . "($values)";

    return $sql;
}

// Fonction pour insérer des tweets factices dans la table "tweet"
function insertFakeTweets($pdo, $faker, $numTweets)
{
    $userCount = $pdo->query("SELECT COUNT(*) as count FROM user")->fetch()['count'];

    $stmt = $pdo->prepare("INSERT INTO tweet (user_id, content) VALUES (:user_id, :content)");
    $i = 0;
    while ($i < $numTweets) {
        $data = [];
        for ($j = 0; $j < 1000; $j++) {
            $userId = $faker->numberBetween(1, $userCount);
            $date = $faker->dateTimeBetween('-2 year', 'now');
            $content = $faker->sentence;
            $data[] = [$userId, $content, $date->format('Y-m-d H:i:s')];
            $i++;
            if ($i >= $numTweets) {
                break;
            }
        }
        $sql = buildInsertStatement("tweet (user_id, content, created_at)", $data);
        $stmt = $pdo->prepare($sql);
        // execute with all values from $data
        $stmt->execute(array_merge(...$data));
    }

    echo "Tweets insérés avec succès!\n";
}


// Configuration de Faker
$faker = Factory::create();
$faker->addProvider(new Lorem($faker));

// Options PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Connexion à la base de données MySQL
    $pdo = new PDO($dsn, $user, $password, $options);

    // Nombre d'utilisateurs et de tweets factices à générer
    $numFakeUsers = 4000000;
    $numFakeTweets = 20000000;

    // Insertion des données factices
    insertFakeUsers($pdo, $faker, $numFakeUsers);
    insertFakeTweets($pdo, $faker, $numFakeTweets);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
} finally {
    // Fermeture de la connexion à la base de données
    $pdo = null;
}
