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
    public const DEFAULT_SUBJECT_TEMPLATE = 'Message "{type}" on transport "{transport}" failed.';

    /**
     * @param string|Address[]|Address|null $to
     * @param string|Address[]|Address|null $cc
     * @param string|Address[]|Address|null $bcc
     * @param string|Address[]|Address|null $replyTo
     * @param string $subjectTemplate
     */
    public function __construct(
        private string|array|Address|null $to = null,
        private string|array|Address|null $cc = null,
        private string|array|Address|null $bcc = null,
        private string|array|Address|null $replyTo = null,
        private string $subjectTemplate = self::DEFAULT_SUBJECT_TEMPLATE,
        private int $priority = Email::PRIORITY_HIGH,
    ) {
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
            ->subject($this->createSubject($message))
            ->text('todo') // todo twig template, html
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

        return $email;
    }

    private function createSubject(ProcessedMessage $message): string
    {
        return \strtr($this->subjectTemplate, [
            '{type}' => $message->type(),
            '{transport}' => $message->transport(),
            '{tags}' => $message->tags(),
            '{failure}' => $message->failure(),
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
