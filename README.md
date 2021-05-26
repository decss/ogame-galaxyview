# Ogame GalaxyView Tool

**Ogame GalaxyView** is a simple Galaxytool analog. It contains serverside web-app and **Tampermonkey** plugin.


### Take a look at [live demo][demo]
To collect data we use this [plugin] for `Tampermonkey` extention, located in `plugin` folder.


## Features
* Automatic data collection via browser plugin
* Searching players by name, status, rank
* Providing info about player (planets, moons, etc)
* Logging players activity (from system view and esp. actions in messages)
* Presenting player's activity chart
* Logging changes like:
    * player name, rating and alliance;
    * player status (vac, inactive, outlaw, etc); 
    * new/destroyed planets and moons; 
    * debris fields 


## Installation
Resolve composer dependencies, edit `.env` config file and run migrations


[demo]: https://dev.soft-szn.ru/ogame/ui
[plugin]: https://github.com/decss/ogame-galaxyview/raw/dev/plugin/ogame-galaxyview.user.js
