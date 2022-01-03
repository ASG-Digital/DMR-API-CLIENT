# DMR-API-CLIENT

1. [Prerequisites](#prerequisites)
2. [Install](#install)
   1. [PHP](#php)
3. [Documentation](#documentation)

### Prerequisites

1. PHP 5.6 or above.
2. CUrl Extension installed in PHP

### Install

#### PHP
For the PHP Client, this library may be installed by copying the `src/php` directory
into the library directory of your project.

if your project uses composer copy/paste the following json into the autoload portion of your `composer.json` file:
```json
...
"autoload": {
    "psr-4": {
        "ASG\\DMRAPI\\": "<path-to-the-library>/Classes/"
    },
    "files": [
        "<path-to-the-library>/version.php",
        "<path-to-the-library>/functions.php"
    ]
},
...
```

if your project does not use composer you may simply load the `load.php` file placed within the
library directory.


### Documentation
Documentation of the client exists for the following languages:
1. [PHP](docs/php/ROOT.md)