# Setup

[Back](../ROOT.md)

```php
<?php

use ASG\DMRAPI\ApiClient;
use ASG\DMRAPI\Basic\CurlHttpClient;
use ASG\DMRAPI\Basic\JsonFileTokenStorage;

// If not using composer
require_once '<path-to-library>/load.php';

// If using composer
require_once '<path-to-vendor>/autoload.php';

$apiClient = new ApiClient(
    new CurlHttpClient(), 
    new JsonFileTokenStorage('<path-to-storage-directory>/tokens.json')
);

```
