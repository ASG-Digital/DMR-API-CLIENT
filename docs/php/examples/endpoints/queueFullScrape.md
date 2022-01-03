# queueFullScrape

[Back](../../ROOT.md)

Note: This section extends the script made in the [Setup](../SETUP.md) section.

```php
<?php

use ASG\DMRAPI\Lookup;

$response = $apiClient->vehicleInfo()->queueFullScrape(Lookup::REG, 'DA56246');
if ($response->isSuccessful() && $response->hasContent()) {
    var_dump($response->getMessage());
}
```