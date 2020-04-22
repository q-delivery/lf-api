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

namespace Gansel\LF\Api;

use function Symfony\Component\String\u;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Webmozart\Assert\Assert;

class Api
{
    /** @var string */
    protected $baseUri;
    /** @var HttpClientInterface */
    protected $client;

    public function __construct(string $baseUri, HttpClientInterface $client)
    {
        $baseUri = trim($baseUri);

        Assert::stringNotEmpty($baseUri);

        if (!(u($baseUri)->startsWith('https://') || u($baseUri)->startsWith('http://'))) {
            throw new \InvalidArgumentException('$baseUri must start with "https://" or "http://".');
        }

        if (u($baseUri)->endsWith('/')) {
            throw new \InvalidArgumentException('$baseUri should not end with a slash.');
        }

        $this->baseUri = $baseUri;
        $this->client = $client;
    }
}
