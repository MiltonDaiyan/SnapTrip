<?php

namespace DaiyanMozumder\SnapTrip\Facades;

use Illuminate\Support\Facades\Facade;

class SnapTrip extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'snaptrip';
    }
}
