<?php
$msg = "";

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

// Verifica se il form è stato sottomesso
if(isset($_POST['submit'])){
    // Recupero dei dati dal form
    $marca = $_POST['marca'];
    $modello = $_POST['modello'];
    $anno = $_POST['anno'];
    $motore = $_POST['motore'];
    $trasmissione = $_POST['trasmissione'];
    $cambio = $_POST['cambio'];
    $alimentazione_type = $_POST['alimentazione_type'];
    $prezzo_listino = $_POST['prezzo_listino'];
    $vel_max = $_POST['vel_max'];
    $cilindrata = $_POST['cilindrata'];
    $potenza = $_POST['potenza'];
    $tempo_0_100 = $_POST['tempo_0_100'];
    $descrizione = $_POST['descrizione'];
    $lunghezza = $_POST['lunghezza'];
    $larghezza = $_POST['larghezza'];
    $altezza = $_POST['altezza'];

    // Inserimento dei dati nella tabella 'cars'
    $sql = "INSERT INTO cars (marca, modello, anno, motore, trasmissione, cambio, alimentazione_type, prezzo_listino, vel_max, cilindrata, potenza, tempo_0_100, descrizione, lunghezza, larghezza, altezza) 
            VALUES ('$marca', '$modello', '$anno', '$motore', '$trasmissione', '$cambio', '$alimentazione_type', '$prezzo_listino', '$vel_max', '$cilindrata', '$potenza', '$tempo_0_100', '$descrizione', '$lunghezza', '$larghezza', '$altezza')";

    if ($conn->query($sql) === TRUE) {
        $id_car = $conn->insert_id; // Recupero dell'ID dell'auto inserita

        // Creazione della cartella per le immagini
        $car_folder = "./cars/$marca-$modello/";
        if (!is_dir($car_folder)) {
            mkdir($car_folder, 0777, true); // Crea la cartella se non esiste già
        }

        // Array associativo per mappare i nomi dei campi di upload alle nuove denominazioni desiderate
        $image_names = array(
            'interni_img' => 'Interni',
            'laterale_img' => 'Laterale',
            'front_img' => 'Front',
            'back_img' => 'Posteriore'
        );

        // Loop attraverso ogni file immagine caricato
        foreach ($_FILES as $key => $value) {
            if (!empty($value['name'])) { // Controlla se l'immagine è stata effettivamente caricata
                $filename = $value['name'];
                $tempname = $value['tmp_name'];
                $extension = pathinfo($filename, PATHINFO_EXTENSION); // Ottieni l'estensione del file
                $new_filename = $image_names[$key] . '.' . $extension; // Crea il nuovo nome del file
                $folder = $car_folder . $new_filename; // Percorso completo del nuovo file

                // Sposta l'immagine nella cartella di destinazione e aggiorna il database
                if (move_uploaded_file($tempname, $folder)) {
                    // Aggiungi il nome del file immagine all'array
                    $image_filenames[] = $new_filename;
                } else {
                    echo "<h3>Errore nel caricare l'immagine nella cartella!</h3>";
                }
            }
        }
    }
    // Chiusura della connessione
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilo Admin</title>
    <link rel="stylesheet" href="./style.css">

    <style>
        header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #3e3f3f; 
        padding: 10px 10px;
        }

        .logo img {
        max-width: 100px;
        max-height: 100px;
        }

        .title {
        font-size: 28px;
        font-weight: bold;
        text-align: center;
        color: #1cdfdf; 
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 30px 0;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        margin-left: 235px;
        }

        .title:hover {
        color: #3e3f3f;
        -webkit-text-stroke: 1px black;
        transition: color 0.3s, -webkit-text-stroke 0.3s;
        }

        header .navigation li {
        list-style: none;
        }

        header .navigation {
        position: relative;
        display: flex;
        }

        header .navigation li a {
        display: inline-block;
        color: #1cdfdf;
        font-weight: 400;
        margin-left: 10px;
        padding: 8px 15px;
        border-radius: 40px;
        text-decoration: none;
        border: 1px solid black;
        }

        header .navigation li a:hover,
        header .navigation li a.active {
        background: white;
        color: #333;
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
        </nav>
    </header>

    <div class="container">
        <h1>Profilo Admin</h1>
        <div class="profile-info">
            <img src="/cartalog/file/img/profile_icon.png" alt="Foto Profilo">
        </div>
        <button class="btn" onclick="openModal()">Aggiungi Informazioni Auto</button>
    </div>

    <!-- Finestra modale -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <!-- Pulsante per chiudere la finestra modale -->
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Inserisci Informazioni Auto</h2>
            <!-- Form per inserire le informazioni della macchina -->
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <div>
                        <label for="marca">Marca:</label>
                        <input type="text" id="marca" name="marca" required>
                    </div>
                    <div>
                        <label for="modello">Modello:</label>
                        <input type="text" id="modello" name="modello" required>
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <label for="anno">Anno:</label>
                        <input type="number" id="anno" name="anno" required>
                    </div>
                    <div>
                        <label for="motore">Motore:</label>
                        <input type="text" id="motore" name="motore">
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <label for="trasmissione">Trasmissione:</label>
                        <input type="text" id="trasmissione" name="trasmissione">
                    </div>
                    <div>
                        <label for="cambio">Cambio(n. vel.):</label>
                        <input type="number" id="cambio" name="cambio">
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <label for="alimentazione_type">Tipo di Alimentazione:</label>
                        <input type="text" id="alimentazione_type" name="alimentazione_type">
                    </div>
                    <div>
                        <label for="prezzo_listino">Prezzo di Listino(€):</label>
                        <input type="number" id="prezzo_listino" name="prezzo_listino">
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <label for="vel_max">Velocità Massima(Km/h):</label>
                        <input type="number" id="vel_max" name="vel_max">
                    </div>
                    <div>
                        <label for="cilindrata">Cilindrata(cc):</label>
                        <input type="number" id="cilindrata" name="cilindrata">
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <label for="potenza">Potenza(CV):</label>
                        <input type="number" id="potenza" name="potenza">
                    </div>
                    <div>
                        <label for="tempo_0_100">Tempo 0-100 km/h(sec.):</label>
                        <input type="number" id="tempo_0_100" name="tempo_0_100" step="0.01">
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <label for="descrizione">Descrizione:</label>
                        <textarea id="descrizione" name="descrizione"></textarea>
                    </div>
                    <div>
                        <label for="lunghezza">Lunghezza(mm):</label>
                        <input type="number" id="lunghezza" name="lunghezza" step="0.01">
                    </div>
                </div>
                <div class="form-group">
                    <div>
                        <label for="larghezza">Larghezza(mm):</label>
                        <input type="number" id="larghezza" name="larghezza" step="0.01">
                    </div>
                    <div>
                        <label for="altezza">Altezza:</label>
                        <input type="number" id="altezza" name="altezza" step="0.01">
                    </div>
                </div>    
                <div class="form-group">
                    <div>
                        <label for="interni_img">Immagine Interni (JPG):</label>
                        <input type="file" id="interni_img" name="interni_img" accept=".jpg">
                    </div>
                    <div>
                        <label for="laterale_img">Immagine Laterale (JPG):</label>
                        <input type="file" id="laterale_img" name="laterale_img" accept=".jpg">
                    </div>
                    <div>
                        <label for="front_img">Immagine Frontale (JPG):</label>
                        <input type="file" id="front_img" name="front_img" accept=".jpg">
                    </div>
                    <div>
                        <label for="back_img">Immagine Posteriore (JPG):</label>
                        <input type="file" id="back_img" name="back_img" accept=".jpg">
                    </div>
                </div>
                <input type="submit" value="Salva Informazioni" name="submit">
            </form>
        </div>
    </div>
</body>
<script src = "./script.js"></script>
</html>