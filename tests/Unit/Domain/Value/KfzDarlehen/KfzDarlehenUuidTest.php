<?php

declare(strict_types=1);

namespace Gansel\LF\Api\Tests\Unit\Domain\Value\KfzDarlehen;

use Gansel\LF\Api\Domain\Value\KfzDarlehen\KfzDarlehenUuid;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class KfzDarlehenUuidTest extends TestCase
{
    /**
     * @test
     */
    public function toIri(): void
    {
        $uuid = KfzDarlehenUuid::fromString(
            '2898b905-7b20-49c3-8c71-08b6f60995ea'
        );

        self::assertSame('/kfz_darlehen/2898b905-7b20-49c3-8c71-08b6f60995ea', $uuid->toIri());
    }

    /**
     * @test
     */
    public function fromString(): void
    {
        $uuid = KfzDarlehenUuid::fromString(
            '2898b905-7b20-49c3-8c71-08b6f60995ea'
        );

        self::assertSame('2898b905-7b20-49c3-8c71-08b6f60995ea', $uuid->toString());
    }

    /**
     * @test
     */
    public function fromUuid(): void
    {
        $uuid = KfzDarlehenUuid::fromUuid(
            Uuid::fromString('2898b905-7b20-49c3-8c71-08b6f60995ea')
        );

        self::assertSame('2898b905-7b20-49c3-8c71-08b6f60995ea', $uuid->toString());
    }
}
