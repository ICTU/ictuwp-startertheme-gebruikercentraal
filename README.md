
# ICTU-WP Theme voor Gebruiker Centraal (2020)

Dit theme is de basis voor alle sites van gebruiker centraal. Het theme is gebaseeerd op [het Timber Starter theme](https://github.com/timber/starter-theme).

## Hoe te gebruiken

Dit theme hoort in de 'themes' folder onder wp-content'. 
Zorg dat (Timber plugin geinstalleerd en actief is[https://nl.wordpress.org/plugins/timber-library/]. 
Dit theme bevat de vormgeving voor meerdere subsites; dit hebben we de 'flavor' genoemd. Je kiest een andere flavor via de customizer. 

Standaard is het GC-smaakje actief, maar we hebben ook vormgeving voor deze subsites:
* Optimaal Digitaal (in ontwikkeling)
* NL Design System (nog opstarten)
* Beeldbank (nog niet in dit theme verwerkt)
* Inclusie (nog niet in dit theme verwerkt)
* conference  (nog niet in dit theme verwerkt)
* volwassenheidsscore  (nog niet in dit theme verwerkt)

### customizer
Gebruikers met beheerders-rechten kunnen de customizer gebruiken. Deze vind je via 
[admin] > Weergave > Customizer
Hier vind je een lijst met opties. Zie de optie 'GC theme-opties'. Hier zie je een selectielijst; je keuze hier beinvloedt de vormgeving van je GC-site.

### BackstopJS Visual Regressie Tests
Er is een Gulp taak voor het runnen van [BackstopJS](https://garris.github.io/BackstopJS/) Visual Regression Tests van de _lokale_ Gebruiker Centraal Homepage (SSL): https://www.gebruikercentraal.test/

- (Run `npm install` om de nieuwe `backstopjs` dependency op te halen)

Vanuit de `./frontend/` folder heb je de volgende Gulp taken:

- `gulp backstop` (== `backstop_test`)
- `gulp backstop_approve`
- `gulp backstop_reference`
- `gulp backstop_init`

De workflow spreekt voor zich. Uitgebreide informatie in de [BackstopJS Documentatie](https://github.com/garris/BackstopJS#the-backstopjs-workflow)

1. `backstop_test` draait een test: de 1e keer faalt deze, want er zijn nog geen 'reference' afbeeldingen
2. `backstop_approve` maakt 'reference' afbeeldingen van de huidige test resultaten. Deze worden bij een volgende test gebruikt

Als het goed is opent een browser met de resultaten.

Falende afbeeldingen worden 'exclude' uit Git.

## Contact
* Victor Zuydweg
* Tamara de Haas: [haas-ontwerp.nl](https://haas-ontwerp.nl/)
* Paul van Buuren: paul@wbvb.nl
* Mark van der Zande: mark@cca.app

## Current version:
5.2.3 - jQuery weer toegevoegd als JS dependency. Kleine bugfix voor zoekrsesultaten.

## Version history
* 5.2.3 - jQuery weer toegevoegd als JS dependency. Kleine bugfix voor zoekrsesultaten.
* 5.2.2 - Paginering aangepast, nummering voor geordende lijsten verbeterd, toegankelijkheidsbugs verholpen.
* 5.2.1 - Gutenbergblock voor citaten; duivelsdrek-oranje toegevoegd als kleur omwille van contrast.
* 5.1.3 - Correct dateformat voor publicatiedatum in datebadge; uitlijning checkbox.
* 5.1.2 - [OD] speelsettaxonomie toegevoegd aan quick edit.
* 5.1.1 - HSTS-header verwijderd.
* 5.1.1 - Gutenberg-blocks for upcoming events, related content, tips overview.
