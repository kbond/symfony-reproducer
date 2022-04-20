<?php

namespace App\View;

use App\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Serialized extends View
{
    private ?string $format = null;

    protected function __construct(private mixed $data, private array $context = [])
    {
    }

    public function as(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function asJson(): self
    {
        return $this->as('json');
    }

    public function asXml(): self
    {
        return $this->as('xml');
    }

    /**
     * @internal
     */
    public function __invoke(Request $request, SerializerInterface $serializer): Response
    {
        // get format in order: 1. manually set, _format request attribute, request accept header
        $format = $this->format ?? $request->getRequestFormat(null) ?? $request->getPreferredFormat();
        $mimeType = $request->getMimeType($format);

        return $this->manipulate(new Response(
            $serializer->serialize($this->data, $format, $this->context),
            Response::HTTP_OK,
            $mimeType ? ['Content-Type' => $request->getMimeType($format)] : [],
        ));
    }
}
