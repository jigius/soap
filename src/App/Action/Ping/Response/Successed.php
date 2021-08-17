<?php
declare(strict_types=1);

namespace Jigius\Soap\App\Action\Ping\Response;

use Acc\Core\PrinterInterface;

final class Successed implements PrinterInterface
{
    private PrinterInterface $orig;

    private array $data;

    public function __construct(PrinterInterface $media)
    {
        $this->orig = $media;
        $this->data = [];
    }

    public function with($key, $val): self
    {
        $obj = new self($this->orig->with($key, $val));
        $obj->data = $this->data;
        $obj->data[$key] = $val;
        return $obj;
    }

    /**
     * @return array
     */
    public function finished(): array
    {
        return
            [
                'errorCode' => 0,
                'value' => $this->orig->finished()
            ];
    }
}
