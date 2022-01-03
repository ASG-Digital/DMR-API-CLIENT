# getEvaluations

[Back](../../ROOT.md)

Note: This section extends the script made in the [Setup](../SETUP.md) section.

```php
<?php

use ASG\DMRAPI\Lookup;

$response = $apiClient->vehicleInfo()->getEvaluations(Lookup::VIN, 'SALLSAAG4AA217485');
if ($response->isSuccessful() && $response->hasContent()) {
    foreach ($response->getData() as $evaluation) {
        var_dump($evaluation);
    }
}
```