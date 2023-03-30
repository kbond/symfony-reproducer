<?php

namespace App\Tests;

use App\Entity\Product;
use App\Routing\ObjectUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ObjectUrlGeneratorTest extends KernelTestCase
{
    /**
     * @test
     * @dataProvider generateForObjectProvider
     */
    public function generate(array $arguments, string $expected): void
    {
        $this->assertSame($expected, $this->router()->generate(...$arguments));
    }

    public static function generateForObjectProvider(): iterable
    {
        yield [['product', ['id' => 7]], '/product/7'];
        yield [['product', ['id' => 7], ObjectUrlGenerator::ABSOLUTE_URL], 'http://localhost/product/7'];

        yield [[new Product(6)], '/product/6'];
        yield [[new Product(6), ['foo' => 'bar']], '/product/6?foo=bar'];
        yield [[new Product(6), ['foo' => 'bar'], ObjectUrlGenerator::ABSOLUTE_URL], 'http://localhost/product/6?foo=bar'];
        yield [[new Product(6), null, ObjectUrlGenerator::ABSOLUTE_URL], 'http://localhost/product/6'];

        yield [[new Product(6), 'admin'], '/admin/product/6?extra=param'];
        yield [[new Product(6), 'admin', ['foo' => 'bar']], '/admin/product/6?extra=param&foo=bar'];
        yield [[new Product(6), 'admin', ['foo' => 'bar'], ObjectUrlGenerator::ABSOLUTE_URL], 'http://localhost/admin/product/6?extra=param&foo=bar'];
        yield [[new Product(6), 'admin', ObjectUrlGenerator::ABSOLUTE_URL], 'http://localhost/admin/product/6?extra=param'];
    }

    private function router(): ObjectUrlGenerator
    {
        return self::getContainer()->get(ObjectUrlGenerator::class);
    }
}
