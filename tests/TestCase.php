<?php declare(strict_types=1);

/**
 * This file is part of CurlToCLI, an OrangePHP, LLC, Project.
 *
 * Copyright © 2022 Orange PHP, LLC.
 *
 * This file is licensed under the MIT License.
 */

namespace OrangePHP\CurlToCLI\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Checks if phpunit was togged in debug mode o rnot.
     * See https://stackoverflow.com/a/12612733/430062.
     *
     * @return bool
     */
    public static function isDebugOn(): bool
    {
        return in_array('--debug', $_SERVER['argv'], true);
    }
}
