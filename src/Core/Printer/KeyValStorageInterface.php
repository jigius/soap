<?php
/**
 * This file is part of the jigius/soap library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) 2021 Jigius <jigius@gmail.com>
 * @link https://github.com/jigius/soap GitHub
 */

declare(strict_types=1);

namespace  Jigius\Soap\Core\Printer;

/**
 * Interface KeyValStorageInterface
 *
 * @package Jigius\Soap\Core\Printer
 */
interface KeyValStorageInterface
{
    /**
     * @param string $realm
     * @param string $key
     * @return bool
     */
    public function known(string $realm, string $key): bool;

    /**
     * @param string $realm
     * @param string $key
     * @return string
     */
    public function value(string $realm, string $key): string;

    /**
     * @param string $realm
     * @param string $key
     * @param $value
     * @return KeyValStorageInterface
     */
    public function append(string $realm, string $key, $value): KeyValStorageInterface;
}
