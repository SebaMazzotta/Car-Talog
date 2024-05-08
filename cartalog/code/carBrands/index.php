<?php
session_start();

// Connessione al database
$host = 'localhost';
$dbname = 'cartalog';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verifica se l'utente è autenticato
if (isset($_SESSION['email'])) {
    // Recupera l'username dell'utente autenticato dal database
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

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marchi di Automobili</title>
    <link rel="stylesheet" href="./style.css">
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
        <div class="column">
            <ul id="column1"> </ul>
        </div>
        <div class="column">
            <ul id="column2"> </ul>
        </div>
        <div class="column">
            <ul id="column3"> </ul>
        </div>
    </div>

    <script src="./script.js"></script>
</body>
</html>
