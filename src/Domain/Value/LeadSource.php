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

namespace Gansel\LF\Api\Domain\Value;

use Webmozart\Assert\Assert;

final class LeadSource
{
    public const LEAD_SOURCE_CONTAO_AKW_FORMULAR = 'akw-formular';
    public const LEAD_SOURCE_CONTAO_IMMO_FORMULAR = 'immo-formular';

    /** @var string */
    private $value;

    private function __construct(string $value)
    {
        Assert::oneOf(
            $value,
            [
                self::LEAD_SOURCE_CONTAO_AKW_FORMULAR,
                self::LEAD_SOURCE_CONTAO_IMMO_FORMULAR,
            ]
        );

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function AKW_Formular(): self
    {
        return new self(self::LEAD_SOURCE_CONTAO_AKW_FORMULAR);
    }

    public static function IMMO_Formular(): self
    {
        return new self(self::LEAD_SOURCE_CONTAO_IMMO_FORMULAR);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
