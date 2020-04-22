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

namespace Gansel\LF\Api\Domain\Value\KfzDarlehen;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class KfzDarlehenUuid
{
    private $uuid;

    private function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    public static function fromString(string $value): self
    {
        return new self(Uuid::fromString($value));
    }

    public static function fromUuid(UuidInterface $uuid): self
    {
        return new self($uuid);
    }

    public function toString(): string
    {
        return $this->uuid->toString();
    }

    public function toIri(): string
    {
        return \Safe\sprintf(
            '/kfz_darlehen/%s',
            $this->uuid->toString()
        );
    }
}
