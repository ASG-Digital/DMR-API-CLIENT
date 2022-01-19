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

The cached endpoint(shown here under) will only look in the data we currently have, and will not attempt to read data from out upstream data source.

```php
<?php
# This is Deprecated
use ASG\DMRAPI\Lookup;

$vehicle = $apiClient->vehicleInfo()->getVehicleCached(Lookup::REG, 'AB12345');

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