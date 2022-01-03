# Login

Note: This section extends the script made in the [Setup](SETUP.md) section.

```php
<?php

// If not already logged in, then login.
if (!($apiClient->getKeyPair() instanceof KeyPair)) {
    $apiClient->login('<client-key>', '<username>', '<password>');
}

```