<?php

namespace App\Tests;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Filesystem\Test\InteractsWithFilesystem;
use Zenstruck\Filesystem\Test\Node\TestFile;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use function Zenstruck\Foundry\create;
use function Zenstruck\Foundry\repository;

class FilesystemTest extends KernelTestCase
{
    use InteractsWithFilesystem, Factories, ResetDatabase;

    /**
     * @test
     */
    public function filesystem_service(): void
    {
        $this->filesystem()
            ->assertNotExists('file.txt')
            ->write('file.txt', 'foo')
            ->assertExists('file.txt')
            ->assertFileExists('file.txt', function(TestFile $file) {
                $this->assertSame('/files/file.txt', $file->url()->toString());
            })
        ;
    }

    /**
     * @test
     */
    public function entities(): void
    {
        $post = create(Post::class, [
            'title' => 'foobar',
            'file' => $this->filesystem()->write('nested/file.txt', 'content')->last()
        ]);

        dd(repository(Post::class)->first()->object()->file->mimeType());
    }
}
