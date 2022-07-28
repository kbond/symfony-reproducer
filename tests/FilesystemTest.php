<?php

namespace App\Tests;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Filesystem\Node\File\PendingFile;
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
    public function entity_save_load_delete(): void
    {
        $this->filesystem()->assertNotExists('nested/file.txt');

        $post = create(Post::class, [
            'title' => 'foobar',
            'file' => $this->filesystem()->write('nested/file.txt', 'content')->last()
        ]);

        $this->assertSame('text/plain', repository(Post::class)->first()->object()->file->mimeType());
        $this->filesystem()->assertExists('nested/file.txt');

        $post->remove();

        $this->filesystem()->assertNotExists('nested/file.txt');
        $post->assertNotPersisted();
    }

    /**
     * @test
     */
    public function entity_delete_file_on_update(): void
    {
        $this->filesystem()->assertNotExists('nested/file.txt');

        $post = create(Post::class, [
            'title' => 'foobar',
            'file' => $this->filesystem()->write('nested/file.txt', 'content')->last()
        ]);

        $this->assertSame('text/plain', repository(Post::class)->first()->object()->file->mimeType());
        $this->filesystem()->assertExists('nested/file.txt');

        $post->file = null;
        $post->save();

        $this->filesystem()->assertNotExists('nested/file.txt');
        $post->assertPersisted();
    }

    /**
     * @test
     */
    public function can_update_file_on_null_file(): void
    {
        $this->filesystem()->assertNotExists('nested/file.txt');

        $post = create(Post::class, [
            'title' => 'foobar',
            'file' => null,
        ]);

        $post->file = $this->filesystem()->write('nested/file.txt', 'content')->last();
        $post->save();

        $this->assertSame('text/plain', repository(Post::class)->first()->object()->file->mimeType());
        $this->filesystem()->assertExists('nested/file.txt');
    }

    /**
     * @test
     */
    public function can_update_file_on_existing_file(): void
    {
        $post = create(Post::class, [
            'title' => 'foobar',
            'file' => $this->filesystem()->write('nested/file1.txt', 'content')->last(),
        ]);

        $post->file = $this->filesystem()->write('nested/file2.png', 'content')->last();
        $post->save();

        $this->assertSame('image/png', repository(Post::class)->first()->object()->file->mimeType());
        $this->filesystem()->assertNotExists('nested/file1.txt');
        $this->filesystem()->assertExists('nested/file2.png');
    }

    /**
     * @test
     */
    public function can_create_with_pending_file(): void
    {
        $this->filesystem()->assertNotExists('composer.json');

        $post = create(Post::class, [
            'title' => 'foobar',
            'file' => new PendingFile(__DIR__.'/../composer.json'),
        ]);

        $this->filesystem()->assertExists('composer.json');
        $this->assertSame('application/json', repository(Post::class)->first()->file->mimeType());
    }

    /**
     * @test
     */
    public function can_update_pending_file_on_null_file(): void
    {
        $post = create(Post::class, [
            'title' => 'foobar',
            'file' => null,
        ]);

        $post->file = new PendingFile(__DIR__.'/../composer.json');
        $post->save();

        $this->assertSame('application/json', repository(Post::class)->first()->object()->file->mimeType());
        $this->filesystem()->assertExists('composer.json');
    }

    /**
     * @test
     */
    public function can_update_pending_file_on_existing_file(): void
    {
        $post = create(Post::class, [
            'title' => 'foobar',
            'file' => $this->filesystem()->write('nested/file1.txt', 'content')->last(),
        ]);

        $post->file = new PendingFile(__DIR__.'/../composer.json');
        $post->save();

        $this->assertSame('application/json', repository(Post::class)->first()->object()->file->mimeType());
        $this->filesystem()->assertNotExists('nested/file1.txt');
        $this->filesystem()->assertExists('composer.json');
    }
}
