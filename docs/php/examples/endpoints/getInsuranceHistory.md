# getInsuranceHistory

[Back](../../ROOT.md)

Note: This section extends the script made in the [Setup](../SETUP.md) section.

```php
<?php

use ASG\DMRAPI\Lookup;

$response = $apiClient->vehicleInfo()->getInsuranceHistory(Lookup::REG, 'DA56246');
if ($response->isSuccessful() && $response->hasContent()) {
    foreach ($response->getData() as $insurance) {
        var_dump($insurance['insurance_certificate_number'] ?? null);
        var_dump($insurance['company'] ?? null);
        var_dump($insurance['status'] ?? null);
        var_dump($insurance['created'] ?? null);
    }
}

```