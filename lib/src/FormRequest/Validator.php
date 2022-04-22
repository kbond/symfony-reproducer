<?php

namespace Zenstruck\FormRequest;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Zenstruck\FormRequest\Form\ObjectForm;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Validator
{
    private const DEFAULT_CSRF_TOKEN_ID = 'form';
    private const CSRF_TOKEN_FIELD = '_token';
    private const CSRF_TOKEN_HEADER = 'X-CSRF-TOKEN';

    private string $csrfTokenId = self::DEFAULT_CSRF_TOKEN_ID;
    private bool $csrfEnabled;

    public function __construct(private Request $request, private ContainerInterface $container)
    {
    }

    final public function disableCsrf(): self
    {
        $this->csrfEnabled = false;

        return $this;
    }

    final public function enableCsrf(string $tokenId = self::DEFAULT_CSRF_TOKEN_ID): self
    {
        $this->csrfTokenId = $tokenId;
        $this->csrfEnabled = true;

        return $this;
    }

    /**
     * @template T of object
     *
     * @param class-string<T>|T|array<string,null|Constraint|Constraint[]> $data
     *
     * @return Form|ObjectForm<T>
     */
    public function validate(string|array|object $data): Form
    {
        if ($this->request->isMethodCacheable()) {
            // not submitted so return empty form
            return new Form();
        }

        $format = $this->detectFormat();
        $decoded = [...$this->decodeRequest($format), ...$this->request->files->all()];
        $form = \is_array($data) ? $this->validateArray($decoded, $data) : $this->validateObject($format, $decoded, $data);

        if (!$this->isCsrfEnabled()) {
            return $form;
        }

        $token = $this->request->request->get(
            self::CSRF_TOKEN_FIELD, // try _token field
            $this->request->headers->get(self::CSRF_TOKEN_HEADER) // try header
        );

        if (!$this->container->has(CsrfTokenManagerInterface::class)) {
            throw new \LogicException('CSRF not enabled in your application.');
        }

        if (!$this->container->get(CsrfTokenManagerInterface::class)->isTokenValid(new CsrfToken($this->csrfTokenId, $token))) {
            // TODO: alternate behaviour: throw TokenMismatch exception to convert to 419 in event listener
            $form->addGlobalError('The CSRF token is invalid. Please try to resubmit the form.');
        }

        return $form;
    }

    private function validateObject(string $format, array $decoded, string|object $data): Form
    {
        if (\is_string($data) && !\class_exists($data)) {
            throw new \InvalidArgumentException(\sprintf('Validation data must be an array, object or "class-string", "%s" given.', $data));
        }

        $isObject = \is_object($data);
        $class = $isObject ? $data::class : $data;
        $context = []; // todo configurable

        if ('form' === $format) {
            $context[AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT] = true;
        }

        if ($isObject) {
            $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $data;
        }

        // TODO catch exceptions and convert to validation errors
        try {
            $object = $this->container->get(DenormalizerInterface::class)->denormalize($decoded, $class, context: $context);
        } catch (NotNormalizableValueException $e) {
            return Form::denormalizationError($decoded, $e);
        }

        if (!\is_object($object)) {
            // todo?
        }

        $form = new ObjectForm($object, $decoded);

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

            $value = \trim($value);

            // TODO: "null trim" the form data (causes issues currently)
//            if ('' === $value) {
//                $value = null;
//            }
        });

        return $data;
    }

    private function isCsrfEnabled(): bool
    {
        if (isset($this->csrfEnabled)) {
            return $this->csrfEnabled;
        }

        if ('html' !== $this->request->getPreferredFormat()) {
            // disable by default if no html
            return $this->csrfEnabled = false;
        }

        // enable by default if available
        return $this->csrfEnabled = $this->container->has(CsrfTokenManagerInterface::class);
    }
}
