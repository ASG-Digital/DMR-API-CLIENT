# getVehicle

[Back](../../ROOT.md)

Note: This section extends the script made in the [Setup](../SETUP.md) section.

```php
<?php

use ASG\DMRAPI\Lookup;

$vehicle = $apiClient->vehicleInfo()->getVehicle(Lookup::REG, 'AB12345');

if ($vehicle->isSuccessful() && $vehicle->hasContent()) {
    var_dump($vehicle->getData());
    
    // it is possible to retrieve information from the "data" with dot notation with the "get" method.
    var_dump('vin: ' . $vehicle->get('vehicle.vin'));
} elseif ($vehicle->hasMessage()) {
    var_dump($vehicle->getMessage());
} else {
    var_dump('Errors with out a message should not happen o.O');
}
```

if you need more data on the vehicle you can also use the following endpoint, but bear in mind that the following endpoint may be slower than the above endpoint.

```php
<?php

use ASG\DMRAPI\Lookup;

$vehicle = $apiClient->vehicleInfo()->getCompleteVehicle(Lookup::REG, 'AB12345');

if ($vehicle->isSuccessful() && $vehicle->hasContent()) {
    var_dump($vehicle->getData());
    
    // it is possible to retrieve information from the "data" with dot notation with the "get" method.
    var_dump('vin: ' . $vehicle->get('vehicle.vin'));
} elseif ($vehicle->hasMessage()) {
    var_dump($vehicle->getMessage());
} else {
    var_dump('Errors with out a message should not happen o.O');
}
```