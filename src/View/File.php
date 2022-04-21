<?php

namespace App\View;

use App\View;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class File extends View
{
    protected function __construct(private \SplFileInfo|string $file)
    {
        parent::__construct();
    }

    public static function attachment(\SplFileInfo|string $file, string $filename = ''): self
    {
        return (new self($file))->asAttachment($filename);
    }

    public static function inline(\SplFileInfo|string $file, string $filename = ''): self
    {
        return (new self($file))->asInline($filename);
    }

    /**
     * @internal
     */
    public function __invoke(Request $request, ContainerInterface $container, ?Response $response = null): Response
    {
        return parent::__invoke($request, $container, new BinaryFileResponse($this->file));
    }

    public function asAttachment(string $filename = ''): self
    {
        return $this->withResponse(
            fn(BinaryFileResponse $r) => $r->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename)
        );
    }

    public function asInline(string $filename = ''): self
    {
        return $this->withResponse(
            fn(BinaryFileResponse $r) => $r->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $filename)
        );
    }

    /**
     * Delete the file after sending.
     */
    public function delete(): self
    {
        return $this->withResponse(fn(BinaryFileResponse $r) => $r->deleteFileAfterSend());
    }
}
