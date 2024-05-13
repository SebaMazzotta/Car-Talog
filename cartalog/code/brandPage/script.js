document.addEventListener("DOMContentLoaded", function() {
    // Otteniamo il parametro del marchio dalla query string
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const brandName = urlParams.get('brand');

    // Costruiamo il percorso del logo del marchio
    const logoPath = `/cartalog/file/img/carBrands/${encodeURIComponent(brandName)}.svg`;

    // Aggiorniamo il logo del marchio nella pagina
    const brandLogoElement = document.getElementById('brand-logo');
    brandLogoElement.src = logoPath;
});
