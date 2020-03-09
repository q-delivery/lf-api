<?php

declare(strict_types=1);

namespace Gansel\LF\Api\Domain\Value\Fall;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class FallUuid
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
}
