<?php

declare(strict_types=1);

/*
 * This file is part of LF-Api.
 *
 * (c) Oskar Stark <oskarstark@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gansel\LF\Api\Tests\Unit\Domain\Value\Fall;

use Gansel\LF\Api\Domain\Value\Fall\FallUuid;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class FallUuidTest extends TestCase
{
    /**
     * @test
     */
    public function toIri(): void
    {
        $uuid = FallUuid::fromString(
            '2898b905-7b20-49c3-8c71-08b6f60995ea'
        );

        self::assertSame('/faelle/2898b905-7b20-49c3-8c71-08b6f60995ea', $uuid->toIri());
    }

    /**
     * @test
     */
    public function fromString(): void
    {
        $uuid = FallUuid::fromString(
            '2898b905-7b20-49c3-8c71-08b6f60995ea'
        );

        self::assertSame('2898b905-7b20-49c3-8c71-08b6f60995ea', $uuid->toString());
    }

    /**
     * @test
     */
    public function fromUuid(): void
    {
        $uuid = FallUuid::fromUuid(
            Uuid::fromString('2898b905-7b20-49c3-8c71-08b6f60995ea')
        );

        self::assertSame('2898b905-7b20-49c3-8c71-08b6f60995ea', $uuid->toString());
    }
}
