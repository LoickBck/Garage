document.addEventListener("DOMContentLoaded", function () {
    const addImage = document.querySelector('#add-image');
    if (addImage) {
        addImage.addEventListener('click', () => {
            // Comptez combien d'éléments form-group existent pour les indices, ex : pres_image_0_url
            const widgetCounter = document.querySelector("#widgets-counter");
            const index = +widgetCounter.value; // Le "+" permet de le transformer en nombre, car value renvoie toujours une chaîne de caractères
            const carImages = document.querySelector("#car_images");
            // Récupérez le prototype dans la div
            const prototype = carImages.dataset.prototype.replace(/__name__/g, index); // Le drapeau "g" indique que nous allons le faire plusieurs fois
            carImages.insertAdjacentHTML('beforeend', prototype);
            widgetCounter.value = index + 1;
            handleDeleteButtons(); // Pour mettre à jour la table des suppressions
        });

        const updateCounter = () => {
            const count = document.querySelectorAll("#car_images div.form-group").length;
            document.querySelector("#widgets-counter").value = count;
        };

        const handleDeleteButtons = () => {
            let deletes = document.querySelectorAll("button[data-action='delete']");
            deletes.forEach(button => {
                button.addEventListener('click', () => {
                    const target = button.dataset.target;
                    const elementTarget = document.querySelector(target);
                    if (elementTarget) {
                        elementTarget.remove(); // Supprimer l'élément
                    }
                });
            });
        };

        updateCounter();
        handleDeleteButtons();
    }
});
