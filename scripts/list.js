function init() {
    const options = {
        loadingText: 'Chargement...',
        noResultsText: 'Aucun résultat trouvé',
        noChoicesText: 'Aucun choix parmi',
        itemSelectText: 'Appuyez pour sélectionner',
        uniqueItemText: 'Seules les valeurs uniques peuvent être ajoutées',
        customAddItemText: 'Seules les valeurs correspondant à des conditions spécifiques peuvent être ajoutées.',
        classNames: {
            containerOuter: ['choices', 'select-form', 'm-0']
        }
    };

    const citySelect = document.querySelector('.city-select');
    if (citySelect) {
        const choicesCity = new Choices(citySelect, options);
    }

    const countrySelect = document.querySelector('.country-select');
    if (countrySelect) {
        const choicesCountry = new Choices(countrySelect, options);
    }

    const authorSelect = document.querySelector('.author-select');
    if (authorSelect) {
        const choicesAuthor = new Choices(authorSelect, options);
    }
}

init();

