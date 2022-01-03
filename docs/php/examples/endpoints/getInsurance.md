# getInsurance

[Back](../../ROOT.md)

Note: This section extends the script made in the [Setup](../SETUP.md) section.

```php
<?php

use ASG\DMRAPI\Lookup;

$response = $apiClient->vehicleInfo()->getInsurance(Lookup::REG, 'DA56246');
if ($response->isSuccessful() as $response->hasContent()) {
    var_dump($response->get('company'));
    var_dump($response->get('status'));
    var_dump($response->get('created'));
}

```