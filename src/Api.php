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
        Assert::stringNotEmpty($baseUri);

        if (u($baseUri)->trim()->endsWith('/')) {
            throw new \InvalidArgumentException('$baseUri should not end with a slash.');
        }

        $this->baseUri = $baseUri;
        $this->client = $client;
    }
}
