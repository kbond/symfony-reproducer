<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Filesystem\Test\InteractsWithFilesystem;
use Zenstruck\Filesystem\Test\Node\TestFile;

class FilesystemTest extends KernelTestCase
{
    use InteractsWithFilesystem;

    public function testSomething(): void
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
}
