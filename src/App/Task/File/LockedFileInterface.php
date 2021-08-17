<?php
declare(strict_types=1);

namespace Jigius\Soap\App\Task\File;

/**
 * Interface LockedFileInterface
 *
 * @package Jigius\Soap\App\Task\File
 */
interface LockedFileInterface
{
    /**
     * Represents the locked state
     */
    public const STATE_UNLOCKED = 0;
    /**
     * Represents the unlocked state
     */
    public const STATE_LOCKED = 1;

    /**
     * Acquires a new lock
     *
     * @param int $flags
     * @return LockedFileInterface
     */
    public function acquired(int $flags = LOCK_SH | LOCK_NB): LockedFileInterface;

    /**
     * Defines a path to a file for locking
     * @param string $path
     * @return $this
     */
    public function withPath(string $path): LockedFileInterface;

    /**
     * Releases the current lock
     *
     * @return LockedFileInterface
     */
    public function released(): LockedFileInterface;

    /**
     * Check the current lock status
     *
     * @return bool Will return TRUE if a lock is in place, FALSE otherwise.
     */
    public function locked(): bool;

    /**
     * Returns a process's ID which holds the lock
     *
     * @return int Returns a PID which holds the lock if it exists or returns `0`.
     */
    public function pid(): int;

    /**
     * Returns stored payload data
     * @return array
     */
    public function payload(): iterable;

    /**
     * Stores a payload data
     * @param iterable $data
     * @return LockedFileInterface
     */
    public function withPayload(iterable $data): LockedFileInterface;
}
