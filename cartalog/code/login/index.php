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

// Variabili per memorizzare i messaggi di successo e di errore
$login_error = $signup_error = $success_message = "";

// Gestione del login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login-submit'])) {
    $email = $_POST['login-email'];
    $password = $_POST['login-password'];
    
    // Esegue una query per verificare le credenziali
    $query = "SELECT * FROM utenti WHERE Email='$email' AND Password='$password'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        // Login riuscito, reindirizza alla pagina home
        $_SESSION['email'] = $email; // Memorizza l'email dell'utente nella sessione
        header("Location: /cartalog/code/homePage/index.php");
        exit(); // Assicura che il codice successivo non venga eseguito dopo il reindirizzamento
    } else {
        $login_error = "Credenziali non valide";
    }
}

// Gestione della registrazione
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup-submit'])) {
    $username = $_POST['signup-username'];
    $email = $_POST['signup-email'];
    $password = $_POST['signup-password'];
    $confirm_password = $_POST['signup-password-confirm'];

    // Verifica che le password coincidano
    if ($password != $confirm_password) {
        $signup_error = "Le password non coincidono";
    } else {
        // Controlla se l'username è già registrato
        $check_username_query = "SELECT * FROM utenti WHERE Username='$username'";
        $check_username_result = $conn->query($check_username_query);

        if ($check_username_result->num_rows > 0) {
            $signup_error = "Il nome utente è già in uso";
        } else {
            // Controlla se l'email è già registrata
            $check_query = "SELECT * FROM utenti WHERE Email='$email'";
            $check_result = $conn->query($check_query);

            if ($check_result->num_rows > 0) {
                $signup_error = "L'email è già registrata";
            } else {
                // Inserisci l'utente nel database
                $insert_query = "INSERT INTO utenti (Username, Email, Password) VALUES ('$username', '$email', '$password')";
                if ($conn->query($insert_query) === TRUE) {
                    // Registrazione riuscita, imposto il messaggio di successo
                    $success_message = "Registrato perfettamente!";
                } else {
                    $signup_error = "Errore durante la registrazione";
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
      <?php if (isset($_SESSION['email'])) { ?>
            <a href="/cartalog/code/login/index.php"></a></li>
            <?php session_destroy(); ?>
          <?php } ?>
    </nav>
</header>

<section class="forms-section">
    <div class="forms">
        <div class="form-wrapper is-active">
            <button type="button" class="switcher switcher-login">
                Login
                <span class="underline"></span>
            </button>

            <form class="form form-login" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <fieldset>
                    <div class="input-block">
                        <label for="login-email">E-mail</label>
                        <input id="login-email" name="login-email" type="email" required>
                    </div>
                    <div class="input-block">
                        <label for="login-password">Password</label>
                        <input id="login-password" name="login-password" type="password" required>
                    </div>
                </fieldset>
                <button type="submit" name="login-submit" class="btn-login">Login</button>
                <!-- Messaggio di errore sul login -->
                <?php if (!empty($login_error)) { ?>
                    <div class="error-message"><?php echo $login_error; ?></div>
                <?php } ?>
                <!-- Messaggio di successo sul login -->
                <?php if (!empty($success_message)) { ?>
                    <div class="success-message"><?php echo $success_message; ?></div>
                <?php } ?>
            </form>
        </div>

        <div class="form-wrapper">
            <button type="button" class="switcher switcher-signup">
                Sign Up
                <span class="underline"></span>
            </button>

            <form class="form form-signup" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <fieldset>
                    <div class="input-block">
                        <label for="signup-username">Username</label>
                        <input id="signup-username" name="signup-username" type="text" required>
                    </div>
                    <div class="input-block">
                        <label for="signup-email">E-mail</label>
                        <input id="signup-email" name="signup-email" type="email" required>
                    </div>
                    <div class="input-block">
                        <label for="signup-password">Password</label>
                        <input id="signup-password" name="signup-password" type="password" required>
                    </div>
                    <div class="input-block">
                        <label for="signup-password-confirm">Confirm password</label>
                        <input id="signup-password-confirm" name="signup-password-confirm" type="password" required>
                    </div>
                </fieldset>
                <button type="submit" name="signup-submit" class="btn-signup">Sign Up</button>
                <!-- Messaggio di errore sulla registrazione -->
                <?php if (!empty($signup_error)) { ?>
                    <div class="error-message"><?php echo $signup_error; ?></div>
                <?php } ?>
                <!-- Messaggio di successo sulla registrazione -->
                <?php if (!empty($success_message)) { ?>
                    <div class="success-message"><?php echo $success_message; ?></div>
                <?php } ?>
            </form>
        </div>
    </div>
</section>

<script src="./script.js"></script>

</body>
</html>
