# getVehicleList

[Back](../../ROOT.md)

Note: This section extends the script made in the [Setup](../SETUP.md) section.

```php
<?php

use ASG\DMRAPI\Filter;

$filters = [
    Filter::make('vehicle.designation.make', Filter::IN, ['Ford', 'BMW']),
    Filter::make('vehicle.model_year', Filter::EQUALS, 2021),
];

$offset = 0;
$limit = 299;

$list = $apiClient->vehicleInfo()->getVehicleList($filters, $offset, $limit);

if ($list->isSuccessful() && $list->hasContent()) {
    $total = $list->getCount();
    if ($total >= 1) {
        foreach($list->getData() as $vehicle) {
            echo '---- Next Vehicle ----' . PHP_EOL;
            var_dump($vehicle['vehicle.designation.make'] ?? null);
            var_dump($vehicle['vehicle.designation.model'] ?? null);
            var_dump($vehicle['vehicle.designation.variant'] ?? null);
            var_dump($vehicle['vehicle.designation.type'] ?? null);
        }
    }
} elseif ($list->hasMessage()) {
    var_dump($list->getMessage());
} else {
    var_dump('Errors with out a message should not happen o.O');
}

```