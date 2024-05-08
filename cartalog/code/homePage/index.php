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

// Logout dell'utente se il parametro 'logout' è impostato nella query string
if (isset($_GET['logout'])) {
    session_destroy(); // Termina la sessione
    header("Location: /cartalog/code/login/index.php"); // Reindirizza alla pagina di login
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Slide Show</title>
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
        <div class="arrow l" onclick="prev()">
            <img src="/cartalog/file/img/l.png" alt="l">
        </div>
        <div class="slide slide-1">
            <div class="caption">
            </div>
        </div>
        <div class="slide slide-2">
            <div class="caption">
            </div>
        </div>
        <div class="slide slide-3">                                 
            <div class="caption">
            </div>
        </div>
        <div class="slide slide-4">
            <div class="caption">
            </div>
        </div>
        <div class="slide slide-5">
            <div class="caption">
            </div>
        </div>
        <div class="arrow r" onclick="next()">
            <img src="/cartalog/file/img/r.png" alt="r">
        </div>

        <div class="dots">
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
            <span class="dot" onclick="currentSlide(4)"></span>
            <span class="dot" onclick="currentSlide(5)"></span>
        </div>
    </div>
    <script src="./script.js"></script>
</body>
</html>
