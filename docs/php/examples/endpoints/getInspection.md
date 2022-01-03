# getInspections

[Back](../../ROOT.md)

Note: This section extends the script made in the [Setup](../SETUP.md) section.

```php
<?php

use ASG\DMRAPI\Lookup;

$response = $apiClient->vehicleInfo()->getInspection(Lookup::VIN, 'SALLSAAG4AA217485');
if ($response->isSuccessful() && $response->hasContent()) {
    var_dump($response->get('latest.type'));
    var_dump($response->get('latest.result'));
    var_dump($response->get('latest.date'));
    var_dump($response->get('never_inspected'));
    var_dump($response->get('no_pending'));
    var_dump($response->get('next_calculated_date'));
}
```