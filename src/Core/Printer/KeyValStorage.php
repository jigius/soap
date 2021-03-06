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

namespace Jigius\Soap\Core\Printer;

/**
 * Class KeyValStorage
 *
 * @package Jigius\Soap\Core\Printer
 */
final class KeyValStorage implements KeyValStorageInterface
{
    /**
     * @var array
     */
    private array $data;

    /**
     * KeyValStorage constructor.
     */
    public function __construct()
    {
        $this->data = [];
    }

    /**
     * @inheritDoc
     */
    public function known(string $realm, string $key): bool
    {
        return array_key_exists($this->hashedKey($realm, $key), $this->data);
    }

    /**
     * @inheritDoc
     */
    public function value(string $realm, string $key): string
    {
        return $this->data[$this->hashedKey($realm, $key)];
    }

    /**
     * @inheritDoc
     */
    public function append(string $realm, string $key, $value): KeyValStorageInterface
    {
        $obj = new self();
        $obj->data = $this->data;
        $obj->data[$this->hashedKey($realm, $key)] = $value;
        return $obj;
    }

    /**
     * @param string $realm
     * @param string $key
     * @return string
     */
    private function hashedKey(string $realm, string $key): string
    {
        return $realm . "@" . $key;
    }
}
