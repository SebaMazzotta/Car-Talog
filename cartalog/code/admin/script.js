// Funzione per aprire la finestra modale
function openModal() {
    var modal = document.getElementById("myModal");
    modal.style.display = "block";
}

// Funzione per chiudere la finestra modale
function closeModal() {
    var modal = document.getElementById("myModal");
    modal.style.display = "none";
}

// Chiudi la finestra modale cliccando al di fuori di essa
window.onclick = function(event) {
    var modal = document.getElementById("myModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}