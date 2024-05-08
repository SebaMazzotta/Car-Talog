document.addEventListener("DOMContentLoaded", function() {
    const carBrands = [
        "Abarth", "Alfa Romeo", "Aston Martin", "Audi", "Bentley", "BMW", 
        "Chevrolet", "Ferrari", "Fiat", "Honda",
        "Hyundai", "Jaguar", "Jeep", "Kia", "Lamborghini",
        "Land Rover", "Lexus", "Lotus", "Maserati", "Mazda",
        "Mclaren", "Mercedes-Benz", "Mitsubishi", "Nissan", "Porsche", "Renault",
        "Subaru", "Suzuki", "Toyota", "Volkswagen"
    ];       
    
    const column1 = document.getElementById("column1");
    const column2 = document.getElementById("column2");
    const column3 = document.getElementById("column3");

    const brandsPerColumn = Math.ceil(carBrands.length / 3);
    for (let i = 0; i < carBrands.length; i++) {
        const listItem = document.createElement("li");
        const link = document.createElement("a");

        // Contenitore per il logo e il nome del marchio
        const brandContainer = document.createElement("div");
        brandContainer.classList.add("brand-container");

        // Logo del marchio
        const logo = document.createElement("img");
        const logoPath = `/cartalog/file/img/carBrands/${encodeURIComponent(carBrands[i])}.svg`;
        logo.src = logoPath;
        logo.alt = `${carBrands[i]} logo`;

        // Nome del marchio
        const brandName = document.createElement("span");
        brandName.textContent = carBrands[i];

        // Aggiunta del logo e del nome del marchio al contenitore
        brandContainer.appendChild(logo);
        brandContainer.appendChild(brandName);

        // Costruisci il percorso dell'URL per la pagina del marchio
        const brandPageURL = `/cartalog/code/brandPage/index.php?brand=${encodeURIComponent(carBrands[i])}`;
        
        link.href = brandPageURL;
        link.appendChild(brandContainer);

        if (i < brandsPerColumn) {
            column1.appendChild(listItem);
        } else if (i < brandsPerColumn * 2) {
            column2.appendChild(listItem);
        } else {
            column3.appendChild(listItem);
        }

        listItem.appendChild(link);
    }
});
