<?php

declare(strict_types=1);

namespace Gansel\LF\Api;

use function Symfony\Component\String\u;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Webmozart\Assert\Assert;

class Api
{
    protected $baseUri;
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
