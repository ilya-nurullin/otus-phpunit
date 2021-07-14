<?php
declare(strict_types=1);

namespace AppUnitTests;

use App\MushroomCollector;
use App\StringPrefix;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use function random_int;

/**
 * @group mushroom
 */
class MushroomCollectorTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @group positive
     */
    public function test1(): void
    {
        $collector = new MushroomCollector('Вася');

        $result = $collector->goHome();

        // забыли assert
        static::assertSame('Вася принёс домой 0 грибов', $result, "Вася сразу пошёл домой, должен был принести 0 грибов");
    }

    public function testFailed(): void
    {
        $collector = new MushroomCollector('Прокоп');
        $collector->collect(11);

        $result = $collector->goHome();

        static::assertSame('Прокоп принёс домой 11 грибов', $result);
    }

    /**
     * @group positive
     */
    public function testMock()
    {
        $collector = new MushroomCollector('Прокоп');
        $stringPrefixMock = \Mockery::mock(StringPrefix::class);

        $stringPrefixMock->shouldReceive('get')->andReturn('123')->once();

        $actual = $collector->ex($stringPrefixMock);

        static::assertSame('123', $actual);
    }

    public function testSpy()
    {
        $collector = new MushroomCollector('Прокоп');
        $stringPrefixSpy = \Mockery::spy(StringPrefix::class);

        $actual = $collector->ex($stringPrefixSpy);

        $stringPrefixSpy->shouldHaveBeenCalled()->get();

        static::assertSame('123', $actual);
    }

    /**
     * @test
     */
    public function superTest2(): void
    {
        $collector = new MushroomCollector('Петя');
        $collector->collect(1);

        $result = $collector->goHome();

        static::assertSame('Петя принёс домой 1 гриб', $result, 'Петя должен принести столько же грибов, сколько собрал');
    }

    public function testIncomplete(): void
    {
        static::markTestIncomplete('Недоделанный тест');
        $collector = new MushroomCollector('Феофан');
        $collector->collect(5);
        $collector->collect(3);
        $collector->collect(6);
        $collector->collect(1);

        $result = $collector->goHome();

        // TODO: посчитать грибы
        static::assertSame('Феофан принёс домой ??? гриба', $result);
    }

    /**
     * @testWith  ["Слава", 2, "гриба"]
     *            ["Миша", 3, "гриба"]
     *            ["Саша", 4, "гриба"]
     *            ["Женя", 5, "грибов"]
     */
    public function test3(string $name, int $collected, string $mushroom): void
    {
        $collector = new MushroomCollector($name);
        $collector->collect($collected);

        $result = $collector->goHome();

        static::assertSame("$name принёс домой $collected $mushroom", $result, "$name должен принести столько же грибов, сколько собрал");
    }

    public function testException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^Слишком много грибов.+Агафон надорвался$/');
        $this->expectExceptionMessage('Слишком много грибов');

        $collector = new MushroomCollector('Агафон');
        $collector->collect(1232);

        $result = $collector->goHome();

//        static::assertSame('Агафон принёс домой 1232 гриба', $result);
    }

    /**
     * @dataProvider someDataProvider
     */
    public function test4(string $name, array $collected, int $expectedCollected, string $mushroom): void
    {
        $collector = new MushroomCollector($name);
        foreach ($collected as $item) {
            $collector->collect($item);
        }

        $result = $collector->goHome();

        static::assertSame("$name принёс домой $expectedCollected $mushroom", $result, "$name должен принести столько же грибов, сколько собрал");
    }

    public function someDataProvider(): array
    {
        return [
            'Жора' => ['Жора', [2, 4], 6, 'грибов'],
            'Витя' => ['Витя', [1, 0], 1, 'гриб'],
            'Вася' => ['Вася', [-3, 3], 0, 'грибов'],
        ];
    }

    public function testFlaky(): void
    {
        static::markTestSkipped('Странная ошибка');
        $collector = new MushroomCollector('Феофан');
        $collected = random_int(2, 3);
        $collector->collect($collected);

        $result = $collector->goHome();

        static::assertSame('Феофан принёс домой 2 гриба', $result, 'Феофан должен принести 2 гриба');
    }
}