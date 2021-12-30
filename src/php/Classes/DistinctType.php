<?php

namespace ASG\DMRAPI;

final class DistinctType
{
    const FUEL = 'fuel';
    const MAKE = 'make';
    const USAGE = 'usage';
    const COLOR = 'color';
    const STATUS = 'status';
    const REG_STATUS = 'reg-status';
    const TYPE = 'type';
    const NORM = 'norm';
    const VALID_TYPES = [
        self::FUEL,
        self::MAKE,
        self::USAGE,
        self::COLOR,
        self::STATUS,
        self::REG_STATUS,
        self::TYPE,
        self::NORM,
    ];
}