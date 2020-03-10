<?php

declare(strict_types=1);

namespace Gansel\LF\Api;

use Gansel\LF\Api\Domain\Value\Fall\FallUuid;
use Webmozart\Assert\Assert;

final class DarlehenApi extends Api
{
    /**
     * @param array<mixed> $payload
     */
    public function add(FallUuid $fallUuid, array $payload): FallUuid
    {
        Assert::notEmpty($payload);
        Assert::keyNotExists($payload, 'fall');

        $payload['fall'] = $fallUuid->toIri();

        $response = $this->client->request(
            'POST',
            \Safe\sprintf('%s/darlehen', $this->baseUri),
            [
                'json' => $payload,
            ]
        );

        return FallUuid::fromString($response->toArray(true)['id']);
    }
}