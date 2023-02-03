<?php

namespace JTD420\PGP\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JTD420\PGP\PGP
 */
class PGP extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \JTD420\PGP\PGP::class;
    }
}
