<?php

namespace App\Tests;

use App\Message\MessageA;
use App\Messenger\MessageChain;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class ChainMessageTest extends KernelTestCase
{
    use InteractsWithMessenger;

    public function testSomething(): void
    {
        $this->bus()->dispatch(
            new MessageChain(
                new MessageA(1),
                new MessageA(2),
            )
        );

        $messages = $this->transport()
            ->queue()
                ->assertCount(1)
                ->assertContains(MessageChain::class, 1)
            ->back()
            ->process(1)
            ->queue()
                ->assertCount(1)
                ->assertContains(MessageA::class, 1)
            ->back()
            ->process(1)
            ->queue()
                ->assertCount(1)
                ->assertContains(MessageA::class, 1)
            ->back()
            ->process(1)
            ->queue()
                ->assertEmpty()
            ->back()
            ->dispatched()
                ->assertContains(MessageA::class, 2)
                ->assertContains(MessageChain::class, 1)
                ->messages(MessageA::class)
        ;

        $this->assertSame(1, $messages[0]->value);
        $this->assertSame(2, $messages[1]->value);
    }
}
