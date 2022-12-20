<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Zenstruck\Filesystem\Test\InteractsWithFilesystem;

class FilesystemTest extends KernelTestCase
{
    use InteractsWithFilesystem;

    /**
     * @beforeClass
     */
    public static function prepare(): void
    {
        (new Filesystem())->dumpFile(__DIR__.'/../var/static-files/file1.txt', 'content');
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSomething(): void
    {
        $this->filesystem()->assertExists('static://file1.txt');
        $this->filesystem()->assertNotExists('public://file2.txt');
        $this->filesystem()->assertNotExists('private://file3.txt');

        $this->filesystem()
            ->write('public://file2.txt', 'content')
            ->write('private://file3.txt', 'content')
        ;

        $this->filesystem()->assertExists('static://file1.txt');
        $this->filesystem()->assertExists('public://file2.txt');
        $this->filesystem()->assertExists('private://file3.txt');
    }

    public static function dataProvider(): iterable
    {
        return \array_fill(0, 1000, [null]);
    }
}
