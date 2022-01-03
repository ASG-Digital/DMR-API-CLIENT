# getInspectionHistory

[Back](../../ROOT.md)

Note: This section extends the script made in the [Setup](../SETUP.md) section.

```php
<?php

use ASG\DMRAPI\Lookup;

$response = $apiClient->vehicleInfo()->getInspectionHistory(Lookup::REG, 'AN75573');
if ($response->isSuccessful() && $response->hasContent()) {
    foreach ($response->getData() as $inspection) {
        var_dump($inspection['company'] ?? null);
        var_dump($inspection['company_cvr'] ?? null);
        var_dump($inspection['company_address'] ?? null);
        var_dump($inspection['vehicle_type'] ?? null);
        var_dump($inspection['type'] ?? null);
        var_dump($inspection['second_type'] ?? null);
        var_dump($inspection['date'] ?? null);
        var_dump($inspection['end_clock'] ?? null);
        var_dump($inspection['odometer'] ?? null);
        var_dump($inspection['result'] ?? null);
    }
}
```