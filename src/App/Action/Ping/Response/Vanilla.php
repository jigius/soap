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

namespace Jigius\Soap\App\Action\Ping\Response;

use Acc\Core\PrinterInterface;
use LogicException;

final class Vanilla implements PrinterInterface
{
    private array $data;

    public function __construct()
    {
        $this->data = [];
    }

    public function with($key, $val): self
    {
        $obj = new self();
        $obj->data = $this->data;
        $obj->data[$key] = $val;
        return $obj;
    }

    /**
     * @return mixed
     */
    public function finished()
    {
        if (empty($this->data["payload"])) {
            throw new LogicException("data is corrupted");
        }
        return $this->data["payload"];
    }
}
