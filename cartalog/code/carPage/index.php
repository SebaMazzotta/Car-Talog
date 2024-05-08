<?php
session_start();

// Connessione al database
$host = 'localhost';
$dbname = 'cartalog';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Verifica se l'utente è autenticato
if (!isset($_SESSION['email']) && !isset($_GET['logout'])) {
    // Se l'utente non è autenticato, reindirizza alla pagina di login
    header("Location: /cartalog/code/login/index.php");
    exit();
}

// Ottenimento dell'ID dalla query string dell'URL
$id_car = $_GET['id'];

// Query per selezionare le informazioni dell'auto corrispondente all'ID
$sql = "SELECT * FROM cars WHERE id_car = $id_car";
$result = $conn->query($sql);

// Controlla se ci sono risultati
if ($result->num_rows > 0) {
    // Ottieni i dati dell'auto
    $row = $result->fetch_assoc();
    $marca = $row["marca"];
    $modello = $row["modello"];
    $anno = $row["anno"];
    $prezzo_listino = $row["prezzo_listino"];
    $descrizione = $row["descrizione"];
    $motore = $row["motore"];
    $trasmissione = $row["trasmissione"];
    $cambio = $row["cambio"];
    $alimentazione = $row["alimentazione_type"];
    $cilindrata = $row["cilindrata"];
    $potenza = $row["potenza"];
    $vel_max = $row["vel_max"];
    $tempo_0_100 = $row["tempo_0_100"];
    $lunghezza = $row["lunghezza"];
    $larghezza = $row["larghezza"];
    $altezza = $row["altezza"];

} else {
    echo "Nessun risultato trovato";
    exit();
}

if (isset($_SESSION['email'])) {
    // Recupera l'username e lo stato di amministratore dell'utente autenticato dal database
    $email = $_SESSION['email'];
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

// Verifica se il pulsante di preferiti è stato cliccato
if (isset($_POST['action']) && ($_POST['action'] == 'favorite' || $_POST['action'] == 'unfavorite')) {
    // Controlla se l'utente è autenticato
    if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        
        // Query per recuperare l'username dall'email
        $sql_username = "SELECT username FROM utenti WHERE email = '$email'";
        $result_username = $conn->query($sql_username);

        if ($result_username->num_rows > 0) {
            $row_username = $result_username->fetch_assoc();
            $username = $row_username['username'];
            
            $id_car = $_POST['id_car'];

            if ($_POST['action'] == 'favorite') {
                // Query per inserire l'auto nei preferiti
                $query_add_favorite = "INSERT INTO Favourite (username, id_car) VALUES ('$username', $id_car)";
                if ($conn->query($query_add_favorite) === TRUE) {
                    echo "Car added to favorites successfully";
                } else {
                    echo "Error adding car to favorites: " . $conn->error;
                }
            } elseif ($_POST['action'] == 'unfavorite') {
                // Query per rimuovere l'auto dai preferiti
                $query_remove_favorite = "DELETE FROM Favourite WHERE username = '$username' AND id_car = $id_car";
                if ($conn->query($query_remove_favorite) === TRUE) {
                    echo "Car removed from favorites successfully";
                } else {
                    echo "Error removing car from favorites: " . $conn->error;
                }
            }
        } else {
            echo "Error retrieving username";
        }
    } else {
        // L'utente non è autenticato, gestisci l'errore o reindirizzalo alla pagina di login
        echo "User not authenticated";
    }
    exit(); // Termina lo script dopo aver gestito la richiesta POST
}

// Controlla se l'auto è nei preferiti per l'utente autenticato
$isFavorite = false;
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sql_check_favorite = "SELECT * FROM Favourite WHERE username = '$username' AND id_car = $id_car";
    $result_check_favorite = $conn->query($sql_check_favorite);
    if ($result_check_favorite->num_rows > 0) {
        $isFavorite = true;
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dettagli dell'auto</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>
    <style>
        .grid-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(3, auto);
            gap: 20px; /* Spazio tra le celle */
        }
        .grid-item {
            border: 0px solid #ccc; /* Aggiungi bordi per separare le celle */
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden; /* Nascondi parti delle immagini che potrebbero fuoriuscire dai container */
        }
        .grid-item img {
            min-width: 100%; /* Fai in modo che l'immagine riempia orizzontalmente il container */
            min-height: 100%; /* Fai in modo che l'immagine riempia verticalmente il container */
        }
        .half-width {
            width: calc(50% - 10px); /* Larghezza della metà del grid container, sottraendo il gap */
        }
        .data-list {
            padding: 0px;
        }
        .data-list ul {
            list-style-type: none;
            padding: 0;
        }
        .data-list ul li {
            margin-bottom: 10px; /* Aggiungo spazio tra i punti */
            display: flex; /* Aggiunto */
            justify-content: space-between; /* Aggiunto */
            font-size: 18px; /* Ingrandisco il testo */
        }
        .data-list ul li span {
            font-weight: bold;
            margin-right: 10px; /* Aggiungo spazio tra l'etichetta e il valore */
            white-space: nowrap; /* Aggiunto per evitare il wrap */
        }
        .like-button {
            font-size: 24px; /* Dimensione del bottone */
            margin-left: 10px; /* Spazio a sinistra del titolo */
            cursor: pointer; /* Cambia il cursore al passaggio sopra il bottone */
        }
        .like-button.liked {
            color: green; /* Cambia il colore del bottone se "liked" */
        }

        .model-viewer {
            height: 400px;
            width: 100%;
            margin: 50px auto;
        }
    </style>
</head>
<body>

<header>
    <!-- Header -->
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

<div class="content">
    <?php if (file_exists("/cartalog/file/models/$marca-$modello.glb")) { ?>
        <!-- Se esiste il file glb, mostra il model-viewer -->
        <model-viewer src="/cartalog/file/models/<?php echo $marca . '-' . $modello; ?>.glb" class="model-viewer" camera-controls camera-orbit="30deg 80deg 50m"
          shadow-intensity="1" touch-action="pan-y"></model-viewe>
    <?php } else { ?>
        <center>
            <!-- Se non esiste il file glb, mostra un messaggio -->
            <p>Nessun file 3D caricato per questa macchina</p>
        </center>
    <?php } ?>
</div>


<div class="container">
    <!-- Contenuto della pagina -->
    <div class="car-name">
        <h2><?php echo $marca . ' ' . $modello; ?>
        <form method="post">
            <input type="hidden" name="id_car" value="<?php echo $id_car; ?>">
            <button class="like-button <?php echo $isFavorite ? 'liked' : ''; ?>" name="action" value="<?php echo $isFavorite ? 'unfavorite' : 'favorite'; ?>">
                <?php echo $isFavorite ? '1' : '0'; ?>
            </button>
        </form>
        </h2>
    </div>

    <!-- Sezione immagini e dati auto -->
    <div class="grid-container">
        <!-- Immagini e dati dell'auto -->
        <!-- Immagine 1 -->
        <div class="grid-item">
            <img src="/cartalog/code/admin/cars/<?php echo $marca . '-' . $modello; ?>/interni.jpg" alt="Interni">
        </div>
        <!-- Dati auto -->
        <div class="grid-item half-width">
            <div class="data-list">
                <ul>
                    <li><span>Marca:</span><?php echo $marca; ?></li>
                    <li><span>Modello:</span><?php echo $modello; ?></li>
                    <li><span>Anno:</span><?php echo $anno; ?></li>
                    <li><span>Prezzo(€):</span><?php echo $prezzo_listino; ?></li>
                    <li><span>Descrizione:</span><?php echo $descrizione; ?></li>
                </ul>
            </div>
        </div>

        <!-- Dati auto -->
        <div class="grid-item half-width">
            <div class="data-list">
                <ul>
                    <li><span>Motore:</span><?php echo $motore; ?></li>
                    <li><span>Trasmissione:</span><?php echo $trasmissione; ?></li>
                    <li><span>Cambio(Vel.):</span><?php echo $cambio; ?></li>
                    <li><span>Alimentazione:</span><?php echo $alimentazione; ?></li>
                    <li><span>Cilindrata(cc):</span><?php echo $cilindrata; ?></li>
                    <li><span>Potenza(CV):</span><?php echo $potenza; ?></li>
                </ul>
            </div>
        </div>

        <!-- Immagine 2 -->
        <div class="grid-item">
            <img src="/cartalog/code/admin/cars/<?php echo $marca . '-' . $modello; ?>/posteriore.jpg" alt="Posteriore">
        </div>

        
        <!-- Immagine 3 -->
        <div class="grid-item">
            <img src="/cartalog/code/admin/cars/<?php echo $marca . '-' . $modello; ?>/front.jpg" alt="Frontale">
        </div>
        <!-- Dati auto -->
        <div class="grid-item half-width">
            <div class="data-list">
                <ul>
                    <li><span>Vel. Massima(Km/h):</span><?php echo $vel_max; ?></li>
                    <li><span>0/100(sec):</span><?php echo $tempo_0_100; ?></li>
                    <li><span>Lunghezza(mm):</span><?php echo $lunghezza; ?></li>
                    <li><span>Larghezza(mm):</span><?php echo $larghezza; ?></li>
                    <li><span>Altezza(mm):</span><?php echo $altezza; ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript per gestire il click sul pulsante "Mi piace"
document.querySelector('.like-button').addEventListener('click', function() {
    // Trova il bottone "Mi piace"
    const likeButton = this;

    // Se l'auto è già nei preferiti, rimuovila
    if (likeButton.classList.contains('liked')) {
        // Effettua una richiesta AJAX per rimuovere l'auto dai preferiti
        const formData = new FormData();
        formData.append('action', 'unfavorite');
        formData.append('id_car', <?php echo $id_car; ?>);

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            // Se la rimozione ha avuto successo, ricarica la pagina
            if (result.trim() === 'Car removed from favorites successfully') {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));

    } else { // Se l'auto non è nei preferiti, aggiungila
        // Effettua una richiesta AJAX per aggiungere l'auto ai preferiti
        const formData = new FormData();
        formData.append('action', 'favorite');
        formData.append('id_car', <?php echo $id_car; ?>);

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(result => {
            console.log(result);
            // Se l'aggiunta ha avuto successo, ricarica la pagina
            if (result.trim() === 'Car added to favorites successfully') {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
});

</script>


</body>
</html>
