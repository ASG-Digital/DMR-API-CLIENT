# Login

[Back](../ROOT.md)

Note: This section extends the script made in the [Setup](SETUP.md) section.

```php
<?php

use ASG\DMRAPI\KeyPair;

// If not already logged in, then login.
if (!($apiClient->getKeyPair() instanceof KeyPair)) {
    $apiClient->login('<client-key>', '<username>', '<password>');
}

```

If you wish to manually refresh the token stored in the `TokenStorage` provided in the [Setup](SETUP.md),
then you can run the following method.
```php
$apiClient->refreshTokens();
```
