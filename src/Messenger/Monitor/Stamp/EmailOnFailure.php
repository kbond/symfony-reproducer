<?php

namespace App\Messenger\Monitor\Stamp;

use App\Messenger\Monitor\Model\ProcessedMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\StampInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final class EmailOnFailure implements StampInterface
{
    public const DEFAULT_SUBJECT_TEMPLATE = 'Message "{type:short}" on transport "{transport}" failed.';
    public const DEFAULT_BODY_TEMPLATE = '{exception}';

    private array $transports;

    /**
     * @param string|Address[]|Address|null $to
     * @param string|Address[]|Address|null $cc
     * @param string|Address[]|Address|null $bcc
     * @param string|Address[]|Address|null $replyTo
     * @param string|string[] $transport
     */
    public function __construct(
        private string|array|Address|null $to = null,
        private string|array|Address|null $cc = null,
        private string|array|Address|null $bcc = null,
        private string|array|Address|null $replyTo = null,
        private string|array|Address|null $from = null,
        private string $subjectTemplate = self::DEFAULT_SUBJECT_TEMPLATE,
        private string $bodyTemplate = self::DEFAULT_BODY_TEMPLATE,
        private int $priority = Email::PRIORITY_HIGH,
        string|array $transport = [],
    ) {
        $this->transports = (array) $transport;
    }

    /**
     * @internal
     *
     * @return self[]
     */
    public static function from(Envelope $envelope): iterable
    {
        foreach ((new \ReflectionClass($envelope->getMessage()))->getAttributes(self::class, \ReflectionAttribute::IS_INSTANCEOF) as $stamp) {
            yield $stamp->newInstance();
        }

        foreach ($envelope->all(self::class) as $stamp) {
            yield $stamp;
        }
    }

    /**
     * @internal
     */
    public function createEmail(ProcessedMessage $message, ?self $default = null): Email
    {
        $email = (new Email())
            ->priority($this->priority)
            ->subject(self::parse($this->subjectTemplate, $message))
            ->text(self::parse($this->bodyTemplate, $message))
            ->to(...self::normalizeAddress($this->to ?? $default?->to))
        ;

        if ($cc = $this->cc ?? $default?->cc) {
            $email->cc(...self::normalizeAddress($cc));
        }

        if ($bcc = $this->bcc ?? $default?->bcc) {
            $email->bcc(...self::normalizeAddress($bcc));
        }

        if ($replyTo = $this->replyTo ?? $default?->replyTo) {
            $email->replyTo(...self::normalizeAddress($replyTo));
        }

        if ($from = $this->from ?? $default?->from) {
            $email->from(...self::normalizeAddress($from));
        }

        if ($transports = $this->transports ?: $default?->transports) {
            $email->getHeaders()->addTextHeader('X-Bus-Transport', \implode(', ', $transports));
        }

        return $email;
    }

    /**
     * @internal
     */
    public function hasTo(): bool
    {
        return (bool) $this->to;
    }

    private static function parse(string $template, ProcessedMessage $message): string
    {
        return \strtr($template, [
            '{type}' => $message->type(),
            '{type:short}' => $message->type()->shortName(),
            '{transport}' => $message->transport(),
            '{tags}' => $message->tags(),
            '{failure}' => $message->failure(),
            '{exception}' => $message->failure()->exception(),
        ]);
    }

    private static function normalizeAddress(string|array|Address $address): array
    {
        if (\is_string($address)) {
            return [new Address($address)];
        }

        if (\is_array($address)) {
            return \array_map(static fn ($address) => self::normalizeAddress($address), $address);
        }

        return [$address];
    }
}
