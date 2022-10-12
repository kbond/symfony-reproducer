<?php

namespace App\Tests;

use App\Entity\Post;
use App\Proxy;
use App\ProxyFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\VarExporter\LazyObjectInterface;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class RealProxyTest extends KernelTestCase
{
    use Factories, ResetDatabase;

    /**
     * @test
     */
    public function can_create_update_delete(): void
    {
        $object = ProxyFactory::create(Post::class, ['title' => 'title1', 'body' => 'body1']);
        $repo = $object->_repo();

        $this->assertInstanceOf(Post::class, $object);
        $this->assertInstanceOf(Proxy::class, $object);
        $this->assertInstanceOf(LazyObjectInterface::class, $object);

        // initial data
        $this->assertSame('title1', $object->getTitle());
        $this->assertSame('body1', $object->body);
        $repo->assert()->exists(['title' => 'title1', 'body' => 'body1']);

        // modify and save itself
        $object->setTitle('title2');
        $object->body = 'body2';
        $object->_save();

        $this->assertSame('title2', $object->getTitle());
        $this->assertSame('body2', $object->body);
        $repo->assert()->exists(['title' => 'title2', 'body' => 'body2']);

        // modify and save "externally"
        self::getContainer()->get('doctrine')->getManager()->clear();
        $post = $repo->first()->object();
        $post->setTitle('title3');
        $post->body = 'body3';
        self::getContainer()->get('doctrine')->getManager()->flush();

        // does not auto-refresh
        $this->assertSame('title2', $object->getTitle());
        $this->assertSame('body2', $object->body);

        // can refresh self
        $object->_refresh();
        $this->assertSame('title3', $object->getTitle());
        $this->assertSame('body3', $object->body);
        $repo->assert()->exists(['title' => 'title3', 'body' => 'body3']);

        // can delete
        $object->_delete();
        $this->assertSame('title3', $object->getTitle());
        $this->assertSame('body3', $object->body);
        $repo->assert()->notExists(['title' => 'title3', 'body' => 'body3']);

        $this->expectException(\RuntimeException::class);
        $object->_refresh();
    }
}
