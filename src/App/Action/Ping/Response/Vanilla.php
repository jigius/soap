<?php
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
