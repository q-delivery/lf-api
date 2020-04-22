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

namespace Gansel\LF\Api\Tests\Unit;

use Gansel\LF\Api\Api;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ApiTest extends TestCase
{
    /**
     * @test
     */
    public function canSetAndGetClientAndBaseUri(): void
    {
        $client = $this->createMock(HttpClientInterface::class);

        $api = new TestApi('https://test.de', $client);

        self::assertSame($client, $api->getClient());
        self::assertSame('https://test.de', $api->getBaseUri());
    }

    /**
     * @test
     */
    public function throwsExceptionIfBaseUriDoesNotStartWithHttpsOrHttp(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new TestApi('foo', $this->createMock(HttpClientInterface::class));
    }

    /**
     * @test
     */
    public function throwsExceptionIfBaseUriIsEmpty(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new TestApi('', $this->createMock(HttpClientInterface::class));
    }

    /**
     * @test
     */
    public function throwsExceptionIfBaseUriIsBlank(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new TestApi(' ', $this->createMock(HttpClientInterface::class));
    }

    /**
     * @test
     */
    public function throwsExceptionIfBaseUriEndsWithSlash(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new TestApi('https://test.de/', $this->createMock(HttpClientInterface::class));
    }

    /**
     * @test
     */
    public function throwsExceptionIfBaseUriStartsWithASpaceAndEndsWithSlash(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new TestApi(' https://test.de/', $this->createMock(HttpClientInterface::class));
    }
}

final class TestApi extends Api
{
    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    public function getClient(): HttpClientInterface
    {
        return $this->client;
    }
}
