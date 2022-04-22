<?php

namespace Zenstruck\FormRequest;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @template T
 */
final class Validator
{
    /**
     * @param class-string<T>|T|array<string,null|Constraint|Constraint[]> $data
     */
    public function __construct(
        private string|array|object $data,
        private Request $request,
        private ContainerInterface $container,
    ) {
    }

    /**
     * @return Form<T>
     */
    public function validate(): Form
    {
        if ($this->request->isMethodCacheable()) {
            // not submitted so return empty form
            return new Form(\is_object($this->data) ? $this->data : null);
        }

        $format = $this->detectFormat();
        $decoded = [...$this->decodeRequest($format), ...$this->request->files->all()];

        if (\is_array($this->data)) {
            return $this->validateArray($decoded, $this->data);
        }

        if (\is_string($this->data) && !\class_exists($this->data)) {
            throw new \InvalidArgumentException(\sprintf('Validation data must be an array, object or "class-string", "%s" given.', $this->data));
        }

        $isObject = \is_object($this->data);
        $class = $isObject ? $this->data::class : $this->data;
        $context = []; // todo configurable

        if ('form' === $format) {
            $context[AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT] = true;
        }

        if ($isObject) {
            $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $this->data;
        }

        // TODO catch exceptions and convert to validation errors
        try {
            $object = $this->container->get(DenormalizerInterface::class)->denormalize($decoded, $class, context: $context);
        } catch (NotNormalizableValueException $e) {
            return Form::denormalizationError($decoded, $e);
        }

        if (\is_object($object)) {
            // todo?
        }

        $form = new Form($object, $decoded);

        foreach ($this->container->get(ValidatorInterface::class)->validate($object) as $violation) {
            // todo: improve adding violations: handle inside form?
            /** @var ConstraintViolationInterface $violation */
            if ('' === $path = $violation->getPropertyPath()) {
                $form->addGlobalError($violation->getMessage());

                continue;
            }

            // todo: better handling of "nested" errors?
            $form->addError(\explode('[', $path)[0], $violation->getMessage());
        }

        return $form;
    }

    private function validateArray(array $decoded, array $data): Form
    {
        $form = new Form();
        $validator = $this->container->get(ValidatorInterface::class);

        foreach (\array_keys($data) as $field) {
            $value = $decoded[$field] ?? null;

            $form->set($field, $value);

            if (null === $constraints = $data[$field]) {
                // empty rule is just "allowed"
                continue;
            }

            foreach ($validator->validate($value, $constraints) as $violation) {
                /** @var ConstraintViolationInterface $violation */
                $form->addError($field, $violation->getMessage());
            }
        }

        return $form;
    }

    private function detectFormat(): string
    {
        if ($format = $this->request->getRequestFormat(null) ?? $this->request->getContentType()) {
            return $format;
        }

        if (!$contentType = $this->request->headers->get('Content-Type')) {
            throw new UnsupportedMediaTypeHttpException('Content-Type header not set.');
        }

        throw new UnsupportedMediaTypeHttpException(\sprintf('Could not detect format from Content-Type "%s".', $contentType));
    }

    private function decodeRequest(string $format): array
    {
        if ('form' === $format) {
            return self::normalizeFormData($this->request->request->all());
        }

        if (!$this->container->get(DecoderInterface::class)->supportsDecoding($format)) {
            throw new UnsupportedMediaTypeHttpException(\sprintf('Format "%s" not supported.', $format));
        }

        if (!$data = $this->request->getContent()) {
            throw new UnprocessableEntityHttpException('Request body is empty.');
        }

        try {
            $data = $this->container->get(DecoderInterface::class)->decode($data, $format);
        } catch (NotEncodableValueException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), $e);
        }

        if (!\is_array($data)) {
            throw new UnprocessableEntityHttpException('Supplied request data could not be converted to an array.');
        }

        return $data;
    }

    private static function normalizeFormData(array $data): array
    {
        \array_walk($data, static function(&$value) {
            if (\is_array($value)) {
                // filter array and process recursively
                $value = self::normalizeFormData(\array_filter($value));

                return;
            }

            if (!\is_string($value)) {
                return;
            }

            // "null trim" the form data
            if ('' === $value = \trim($value)) {
                $value = null;
            }
        });

        return $data;
    }
}
