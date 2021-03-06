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

use Gansel\LF\Api\Domain\Value\Fall\FallUuid;
use Webmozart\Assert\Assert;

final class DarlehenApi extends Api
{
    /**
     * @param array<mixed> $payload
     *
     * @return array<mixed>
     */
    public function create(FallUuid $fallUuid, array $payload): array
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

        return $response->toArray(true);
    }
}
