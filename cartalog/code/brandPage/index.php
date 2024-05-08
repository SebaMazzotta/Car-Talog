<?php
session_start();

// Connessione al database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cartalog";

// Inizializzazione della connessione
$conn = new mysqli($servername, $username, $password, $dbname);

// Controllo della connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Verifica se l'utente è autenticato
if (isset($_SESSION['email'])) {
    // Recupera l'username dell'utente autenticato dal database
    $email = $conn->real_escape_string($_SESSION['email']);
    $sql = "SELECT username, admin FROM utenti WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $username = $row['username'];
        $isAdmin = $row['admin'];
    } else {
        // Se l'utente non è trovato nel database, effettua il logout
        header("Location: /cartalog/code/login/index.php?logout=true");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marchio</title>
    <link rel="stylesheet" href="./style.css">
    <style>
        .car-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .car-box {
            width: calc(33.33% - 10%);
            text-align: center;
            margin-bottom: 20px;
        }

        .car-box img {
            width: 100%;
            max-width: 100%;
            height: auto;
            border: 1px solid #ccc;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <img src="/cartalog/file/img/logo.png" alt="Logo">
    </div>
    <div class="title">car-talog</div>

    <nav class="navigation">
            <li><a href="/cartalog/code/homePage/index.php">Home</a></li>
            <li><a href="/cartalog/code/carBrands/index.php">Car Brand</a></li>
            <?php if (!isset($_SESSION['email']) || isset($_GET['logout'])) { ?>
                <li><a href="/cartalog/code/login/index.php">Login</a></li>
            <?php } ?>
            <?php if (isset($_SESSION['email'])) { 
                if ($isAdmin == 1) { ?>
                    <li class="dropdown">
                        <a href="#" class="dropbtn"><?php echo $username; ?></a>
                        <div class="dropdown-content">
                            <a href="/cartalog/code/admin/index.php">Panel</a>
                            <a href="/cartalog/code/login/index.php?logout=true">Logout</a>
                        </div>
                    </li>
                <?php } else { ?>
                    <li class="dropdown">
                        <a href="#" class="dropbtn"><?php echo $username; ?></a>
                        <div class="dropdown-content">
                            <a href="/cartalog/code/profile/index.php">Profile</a>
                            <a href="/cartalog/code/login/index.php?logout=true">Logout</a>
                        </div>
                    </li>
                <?php } ?>
            <?php } ?>
        </nav>
</header>

<div class="container">
    <div class="brand-header">
        <img id="brand-logo" src="" alt="Logo">
        <div id="linea"></div>
        <?php
        // Verifica se è stato passato il parametro 'brand' nella query string
        if(isset($_GET['brand'])) {
            // Pulisci e sanitizza il parametro 'brand'
            $brand = $conn->real_escape_string($_GET['brand']);

            // Query per recuperare le macchine della marca specificata
            $sql = "SELECT * FROM cars WHERE marca = '$brand'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $counter = 0;
                // Output dei dati di ogni macchina
                while($row = $result->fetch_assoc()) {
                    if($counter % 3 == 0) {
                        // Inizio di una nuova riga
                        echo '<div class="car-row">';
                    }

                    // Costruisci il percorso dell'immagine
                    $imagePath = "/cartalog/code/admin/cars/" . $row['marca'] . "-" . $row['modello'] . "/Laterale.jpg";

                    // Stampa il riquadro della macchina con la foto laterale e il modello
                    echo '<div class="car-box">';
                    echo '<a href="/cartalog/code/carPage/index.php?id=' . $row['id_car'] . '">'; // Aggiunto il link alla pagina dinamica
                    echo '<img src="' . $imagePath . '" alt="Foto Laterale">';
                    echo '<p>' . $row['modello'] . '</p>';
                    echo '</a>';
                    echo '</div>';

                    $counter++;

                    if($counter % 3 == 0 || $counter == $result->num_rows) {
                        // Fine della riga o fine dei risultati
                        echo '</div>'; // Chiude la riga
                    }
                }
            } else {
                echo "Nessuna macchina trovata per questa marca";
            }
        } else {
            echo "Parametro 'brand' non specificato nella query string";
        }
        ?>
    </div>
</div>

<script src="./script.js"></script>
</body>
</html>
