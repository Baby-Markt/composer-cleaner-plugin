<?php

namespace Babymarkt\Composer\Cleaner;

/**
 * Global function glob mock
 * @see \glob()
 */
function glob($pattern, $flags = null)
{
    return GlobTester::$callback !== null
        ? call_user_func(GlobTester::$callback, $pattern, $flags)
        : \glob($pattern, $flags);
}

class GlobTester {

    static public $callback = null;

}