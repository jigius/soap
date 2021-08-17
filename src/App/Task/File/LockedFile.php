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

namespace Jigius\Soap\App\Task\File;

use RuntimeException;
use DomainException;
use LogicException;
use JsonException;

/**
 * Class LockedFile
 *
 * @package Jigius\Soap\App\Task\File
 */
final class LockedFile implements LockedFileInterface
{
    /**
     * Lock file pointer
     *
     * @var resource
     */
    private $lock;
    /**
     * mode
     *
     * @var int
     */
    private int $mode;
    /**
     * @var string|null
     */
    private ?string $pathfile;
    /**
     * Lock status
     *
     * @var int
     */
    private int $status;

    /**
     * @var iterable
     */
    private iterable $data;

    /**
     * FLock constructor
     */
    public function __construct()
    {
        $this->pathfile = null;
        $this->status = LockedFileInterface::STATE_UNLOCKED;
        $this->mode = 0;
        $this->lock = null;
        $this->data = [
            'payload' => []
        ];
    }

    /**
     * @inheritDoc
     */
    public function withPath(string $path): self
    {
        $obj = $this->blueprinted();
        $obj->pathfile = $path;
        return $obj;
    }

    /**
     * @inheritDoc
     * @throws JsonException
     */
    public function acquired($flags = LOCK_SH | LOCK_NB): self
    {
        if ($this->lock !== null) {
            throw new LogicException("a lock is acquired already");
        }
        if ($this->pathfile === null) {
            throw new LogicException("a pathfile is not defined");
        }
        error_clear_last();
        $lock = fopen($this->pathfile, "c+");
        if ($lock === false) {
            throw
                new DomainException(
                    "Unable to acquires a lock under the file=`$this->pathfile`",
                    0,
                    new RuntimeException(
                        error_get_last()['message'] ?? "unknown",
                    )
                );
        }
        $status = flock($lock, $flags);
        $obj = $this->blueprinted();
        $obj->lock = $lock;
        if ($status === true) {
            ob_start();
            if (@fpassthru($lock) === false) {
                throw new DomainException("Couldn't fetch content from a file");
            }
            $input = ob_get_clean();
            if (!empty($input)) {
                try {
                    $data =
                        json_decode(
                            $input,
                            true,
                            512,
                            JSON_THROW_ON_ERROR
                        );
                } catch (JsonException $ex) {
                    throw new DomainException("data is corrupted", 0, $ex);
                }
                if (
                    isset($data['pid']) && !is_int($data['pid']) ||
                    !isset($data['payload']) || !is_iterable($data['payload'])
                ) {
                    throw new DomainException("data is corrupted");
                }
                $obj->data = $data;
            }
            if ($flags & LOCK_EX) {
                $obj->data['pid'] = getmypid();
                $obj->pushData($obj->data);
            }
            $obj->status = LockedFileInterface::STATE_LOCKED;
        }
        $obj->mode = $flags;
        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function released(): self
    {
        if ($this->lock === null) {
            throw new LogicException("a lock is not acquired already");
        }
        if ($this->status === LockedFileInterface::STATE_UNLOCKED) {
            return $this;
        }
        error_clear_last();
        $status = flock($this->lock, LOCK_UN);
        if ($status === false) {
            throw
            new DomainException(
                "Unable to release a locked file=`$this->pathfile`",
                0,
                new RuntimeException(
                    error_get_last()['message'] ?? "unknown",
                )
            );
        }
        $obj = $this->blueprinted();
        if ($this->mode & LOCK_EX) {
            if (empty($this->data['payload'])) {
                @fclose($this->lock);
                @unlink($this->pathfile);
            } else {
                $data = $this->data;
                unset($data['pid']);
                $this->pushData($data);
                @fclose($this->lock);
            }
        }
        $obj->status = LockedFileInterface::STATE_UNLOCKED;
        $obj->mode = 0;
        $obj->lock = null;
        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function locked(): bool
    {
        if ($this->lock === null) {
            throw new LogicException("there was no try to acquire a lock");
        }
        return $this->status === LockedFileInterface::STATE_LOCKED;
    }

    /**
     * @inheritDoc
     */
    public function pid(): int
    {
        if (!$this->locked()) {
            throw new LogicException("The lock is not acquired");
        }
        return (int)($this->data['pid'] ?? 0);
    }

    /**
     * @inheritDoc
     */
    public function withPayload(iterable $data): LockedFileInterface
    {
        if (!$this->locked()) {
            throw new LogicException("The lock is not acquired");
        }
        if (!($this->mode & LOCK_EX)) {
            throw new LogicException("The lock is not exclusive");
        }
        $obj = $this->blueprinted();
        $obj->data['payload'] = $data;
        $obj->pushData($obj->data);
        return $obj;
    }

    /**
     * @inheritDoc
     */
    public function payload(): iterable
    {
        if (!$this->locked()) {
            throw new LogicException("The lock is not acquired");
        }
        return $this->data['payload'];
    }

    /**
     * Clones the instance
     *
     * @return $this
     */
    private function blueprinted(): self
    {
        $obj = new self();
        $obj->lock = $this->lock;
        $obj->mode = $this->mode;
        $obj->pathfile = $this->pathfile;
        $obj->status = $this->status;
        $obj->data = $this->data;
        return $obj;
    }

    /**
     * @param iterable $data
     */
    private function pushData(iterable $data): void
    {
        if (@fseek($this->lock, 0) === -1) {
            throw new RuntimeException("Couldn't truncate a file", 0);
        }
        try {
            $eData = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        } catch (JsonException $ex) {
            throw new DomainException("data is corrupted", 0, $ex);
        }
        if (($length = @fwrite($this->lock, $eData)) === false) {
            throw new RuntimeException("Couldn't write data into a file", 0);
        }
        ftruncate($this->lock, $length);
    }
}
