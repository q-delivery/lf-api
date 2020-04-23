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

namespace Gansel\LF\Api\Tests\Unit\Domain\Value;

use Gansel\LF\Api\Domain\Value\LeadSource;
use PHPUnit\Framework\TestCase;

final class LeadSourceTest extends TestCase
{
    /**
     * @test
     */
    public function constants(): void
    {
        self::assertSame('akw-formular', LeadSource::LEAD_SOURCE_CONTAO_AKW_FORMULAR);
        self::assertSame('immo-formular', LeadSource::LEAD_SOURCE_CONTAO_IMMO_FORMULAR);
    }

    /**
     * @test
     *
     * @dataProvider fromStringInvalidProvider
     */
    public function fromStringThrowsInvalidArgumentExceptionOn(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);

        LeadSource::fromString($value);
    }

    /**
     * @return \Generator<array<mixed>>
     */
    public function fromStringInvalidProvider(): \Generator
    {
        yield [''];
        yield [' '];
        yield ['special-formular'];
    }

    /**
     * @test
     *
     * @dataProvider fromStringValidProvider
     */
    public function fromString(string $value): void
    {
        $leadSource = LeadSource::fromString($value);

        self::assertSame($value, $leadSource->toString());
    }

    /**
     * @return \Generator<array<string>>
     */
    public function fromStringValidProvider(): \Generator
    {
        yield [LeadSource::LEAD_SOURCE_CONTAO_AKW_FORMULAR];
        yield [LeadSource::LEAD_SOURCE_CONTAO_IMMO_FORMULAR];
    }

    /**
     * @test
     *
     * @dataProvider definedProvider
     */
    public function defined(string $expected, LeadSource $leadSource): void
    {
        self::assertSame($expected, $leadSource->toString());
    }

    /**
     * @return \Generator<array{0: string, 1: LeadSource}>
     */
    public function definedProvider(): \Generator
    {
        yield ['akw-formular', LeadSource::AKW_Formular()];
        yield ['immo-formular', LeadSource::IMMO_Formular()];
    }
}
