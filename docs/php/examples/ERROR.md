# Error Handling

[Back](../ROOT.md)

All endpoints should always return an instance of the `ASG\DMRAPI\ApiResponse` class.

```php
<?php

use ASG\DMRAPI\Lookup;

$response = $apiClient->vehicleInfo()->getVehicle(Lookup::REG, 'AB12345');

if ($response->isSuccessful()) {
    // Do success stuff..
} else {
    if ($response->hasException()) {
        // If you have some system of logging exception you can do it this way.
        MyLogger::logException($response->getException());
    }
    // If the response doesn't have an Exception,
    // then it means the request response was a non success response.
    if ($response->hasMessage()) {
        MyLogger::log($response->getMessage())
    }
}
```