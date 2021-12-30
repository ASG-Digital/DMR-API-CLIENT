<?php

namespace ASG\DMRAPI;

final class Lookup
{
    const ID = 'id';
    const VIN = 'vin';
    const REG = 'reg';
    const VALID_TYPES = [
        self::ID,
        self::REG,
        self::VIN,
    ];
}