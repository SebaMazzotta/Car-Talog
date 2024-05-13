<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="./style.css"> 
    <style>
        .centered-container {
            width: 100%; /* Larghezza massima */
            max-width: 1200px; /* Larghezza massima del container */
            margin: 0 auto; /* Centra il container orizzontalmente */
            padding: 0 20px; /* Spazio vuoto a destra e sinistra del container */
        }

        .profile-container {
            text-align: center;
            margin-top: 50px; /* Aggiungi spazio sopra */
        }

        .profile img {
            width: 100px; /* Modifica la dimensione dell'icona del profilo */
            height: 100px;
            border-radius: 50%;
            vertical-align: middle;
        }

        .username {
            display: block; /* Rendi il messaggio di benvenuto un blocco per centrarlo */
            margin-top: 10px; /* Aggiungi spazio sopra */
        }

        .car-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .car-box {
            width: 30%; /* Larghezza fissa del container */
            max-width: 300px; /* Larghezza massima del container */
            text-align: center;
            margin-bottom: 20px;
        }

        .car-box img {
            width: 180px;
            max-width: auto;
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
        </nav>
    </header>

    <div class="profile-container">
        <?php 
        session_start();
        if (isset($_SESSION['email'])) { 
            $host = 'localhost';
            $dbname = 'cartalog';
            $username = 'root';
            $password = '';

            $conn = new mysqli($host, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $email = $_SESSION['email'];
            $sql = "SELECT username FROM utenti WHERE email = '$email'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $username = $row['username'];
            } else {
                header("Location: /cartalog/code/login/index.php?logout=true");
                exit();
            }
        ?>
            <div class="profile">
                <img src="/cartalog/file/img/profile_icon.png" alt="Profile Icon">
                <span class="username">Welcome <?php echo $username; ?>!</span>
            </div>
        <?php } ?>
    </div>

    <div class="container">
        <div class="brand-header">
            <div class="centered-container">
                <div class="car-row">
                    <?php
                    $column_count = 1;
                    $total_page_width = 100; // Larghezza totale della pagina in percentuale
                    $column_width = (100 / 3) - 20; // Larghezza massima di ogni colonna in percentuale

                    // Verifica se l'utente è autenticato
                    if (isset($_SESSION['email'])) {
                        // Recupera l'id utente autenticato dal database
                        $email = $conn->real_escape_string($_SESSION['email']);
                        $sql_id = "SELECT username FROM utenti WHERE email = '$email'";
                        $result_id = $conn->query($sql_id);

                        if ($result_id->num_rows > 0) {
                            $row_id = $result_id->fetch_assoc();
                            $username = $row_id['username'];

                            // Query per recuperare gli ID delle macchine preferite dell'utente
                            $sql_cars = "SELECT id_car FROM favourite WHERE username = '$username'";
                            $result_cars = $conn->query($sql_cars);

                            if ($result_cars->num_rows > 0) {
                                while($row_car = $result_cars->fetch_assoc()) {
                                    $car_id = $row_car['id_car'];
                                    // Query per recuperare la marca e il modello della macchina corrispondente all'ID
                                    $sql_car_info = "SELECT marca, modello FROM cars WHERE id_car = $car_id";
                                    $result_car_info = $conn->query($sql_car_info);
                                    if ($result_car_info->num_rows > 0) {
                                        $row_car_info = $result_car_info->fetch_assoc();
                                        // Costruisci il percorso dell'immagine
                                        $imagePath = "/cartalog/code/admin/cars/" . $row_car_info['marca'] . "-" . $row_car_info['modello'] . "/Laterale.jpg";
                                        // Apri il link attorno all'immagine e al testo
                                        echo '<a href="/cartalog/code/carPage/index.php?id=' . $car_id . '" class="car-box">';
                                        echo '<img src="' . $imagePath . '" alt="' . $row_car_info['marca'] . ' ' . $row_car_info['modello'] . '">';
                                        echo '<p>' . $row_car_info['marca'] . ' ' . $row_car_info['modello'] . '</p>';
                                        // Chiudi il link
                                        echo '</a>';
                                        // Controlla se la colonna è l'ultima di una riga
                                        if ($column_count % 3 == 0) {
                                            echo '</div><div class="car-row">'; // Chiudi la riga e inizia una nuova riga
                                        }
                                        $column_count++;
                                    }
                                }
                                // Chiudi l'ultima riga se non è già stata chiusa
                                if (($column_count - 1) % 3 != 0) {
                                    echo '</div>';
                                }
                            } else {
                                echo "Nessuna macchina preferita trovata per questo utente";
                            }
                        } else {
                            echo "ID utente non trovato nel database";
                        }
                    } else {
                        echo "Utente non autenticato";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="./script.js"></script>
</body>
</html>
