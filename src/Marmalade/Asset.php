<?php

namespace App\Marmalade;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Asset
{
    public function __construct(public readonly string $path, public readonly \SplFileInfo|string $data)
    {
    }

    public function content(): string
    {
        return match(true) {
            is_string($this->data) => $this->data,
            $this->data instanceof \SplFileInfo && !$this->data->isDir() => file_get_contents($this->data),
            default => throw new \LogicException(sprintf('Invalid data for asset "%s".', $this->path))
        };
    }
}
