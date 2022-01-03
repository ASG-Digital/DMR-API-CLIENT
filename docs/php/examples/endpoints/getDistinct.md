# getDistinct

[Back](../../ROOT.md)

Note: This section extends the script made in the [Setup](../SETUP.md) section.

```php
<?php

use ASG\DMRAPI\DistinctType;

$fuels = $apiClient->vehicleInfo()->getDistinct(DistinctType::FUEL);

if ($fuels->isSuccessful() && $fuels->hasContent()) {
    var_dump($fuels->getData());
} elseif ($fuels->hasMessage()) {
    var_dump($fuels->getMessage());
} else {
    var_dump('Errors with out a message should not happen o.O');
}
```

The above code will `var_dump` an array containing the unique fuels from the vehicles in the dataset.

Note: the `DistinctType` class contains constants with the other data that is accessible through this endpoint.