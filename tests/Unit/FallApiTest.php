<?php

declare(strict_types=1);

namespace Gansel\LF\Api\Tests\Unit;

use Gansel\LF\Api\FallApi;
use PHPUnit\Framework\TestCase;

final class FallApiTest extends TestCase
{
    /**
     * @test
     *
     * @param array<mixed> $expected
     * @param array<mixed> $payload
     *
     * @dataProvider modifyPayloadProvider
     */
    public function modifyPayload(array $expected, array $payload): void
    {
        self::assertSame(
            $expected,
            FallApi::modifyPayload($payload)
        );
    }

    /**
     * @return \Generator<array<mixed>>
     */
    public function modifyPayloadProvider(): \Generator
    {
        yield 'no-change' => [
            [
                'foo' => 'bar',
            ],
            [
                'foo' => 'bar',
            ],
        ];

        yield 'darlehensnehmer-person1' => [
            [
                'anmerkungen' => 'Das ist ein toller Kunden-Kommentar!', // darf null sein
                'darlehensnehmer' => [
                    'person1' => [
                        'anrede' => 'Herr',
                        'vorname' => 'Oskar',
                        'nachname' => 'Stark',
                        'strasse' => 'Heerstraße 78',
                        'adresszusatz' => null,
                        'plz' => '14055',
                        'ort' => 'Berlin',
                        'telefonRufnummer' => '+491754968453',
                        'mobilRufnummer' => null,
                        'email' => 'oskarstark@googlemail.com',
                    ],
                ],
            ],
            [
                'anmerkungen' => 'Das ist ein toller Kunden-Kommentar!', // darf null sein
                'darlehensnehmer' => [
                    'person1' => [
                        'anrede' => 'Herr',
                        'vorname' => 'Oskar',
                        'nachname' => 'Stark',
                    ],
                    'strasse' => 'Heerstraße 78',
                    'adresszusatz' => null,
                    'plz' => '14055',
                    'ort' => 'Berlin',
                    'telefonRufnummer' => '+491754968453',
                    'mobilRufnummer' => null,
                    'email' => 'oskarstark@googlemail.com',
                ],
            ],
        ];

        yield 'darlehensnehmer-person1-und-person2' => [
            [
                'anmerkungen' => 'Das ist ein toller Kunden-Kommentar!', // darf null sein
                'darlehensnehmer' => [
                    'person1' => [
                        'anrede' => 'Herr',
                        'vorname' => 'Oskar',
                        'nachname' => 'Stark',
                        'strasse' => 'Heerstraße 78',
                        'adresszusatz' => null,
                        'plz' => '14055',
                        'ort' => 'Berlin',
                        'telefonRufnummer' => '+491754968453',
                        'mobilRufnummer' => null,
                        'email' => 'oskarstark@googlemail.com',
                    ],
                    'person2' => [
                        'anrede' => 'Frau',
                        'vorname' => 'Melany',
                        'nachname' => 'Stark',
                        'strasse' => 'Heerstraße 78',
                        'adresszusatz' => null,
                        'plz' => '14055',
                        'ort' => 'Berlin',
                        'telefonRufnummer' => '+491754968453',
                        'mobilRufnummer' => null,
                        'email' => 'oskarstark@googlemail.com',
                    ],
                ],
            ],
            [
                'anmerkungen' => 'Das ist ein toller Kunden-Kommentar!', // darf null sein
                'darlehensnehmer' => [
                    'person1' => [
                        'anrede' => 'Herr',
                        'vorname' => 'Oskar',
                        'nachname' => 'Stark',
                    ],
                    'person2' => [
                        'anrede' => 'Frau',
                        'vorname' => 'Melany',
                        'nachname' => 'Stark',
                    ],
                    'strasse' => 'Heerstraße 78',
                    'adresszusatz' => null,
                    'plz' => '14055',
                    'ort' => 'Berlin',
                    'telefonRufnummer' => '+491754968453',
                    'mobilRufnummer' => null,
                    'email' => 'oskarstark@googlemail.com',
                ],
            ],
        ];

        yield 'darlehensnehmer-makler' => [
            [
                'anmerkungen' => 'Das ist ein toller Kunden-Kommentar!', // darf null sein
                'darlehensnehmer' => [
                    'person1' => [
                        'anrede' => 'Herr',
                        'vorname' => 'Oskar',
                        'nachname' => 'Stark',
                        'strasse' => 'Heerstraße 78',
                        'adresszusatz' => null,
                        'plz' => '14055',
                        'ort' => 'Berlin',
                        'telefonRufnummer' => '+491754968453',
                        'mobilRufnummer' => null,
                        'email' => 'oskarstark@googlemail.com',
                    ],
                ],
                'makler' => [
                    'person' => [
                        'anrede' => 'Herr',
                        'vorname' => 'Oskar',
                        'nachname' => 'Stark',
                        'strasse' => 'Heerstraße 78',
                        'adresszusatz' => null,
                        'plz' => '14055',
                        'ort' => 'Berlin',
                        'telefonRufnummer' => '+491754968453',
                        'mobilRufnummer' => null,
                        'email' => 'oskarstark@googlemail.com',
                    ],
                ],
            ],
            [
                'anmerkungen' => 'Das ist ein toller Kunden-Kommentar!', // darf null sein
                'darlehensnehmer' => [
                    'person1' => [
                        'anrede' => 'Herr',
                        'vorname' => 'Oskar',
                        'nachname' => 'Stark',
                    ],
                    'strasse' => 'Heerstraße 78',
                    'adresszusatz' => null,
                    'plz' => '14055',
                    'ort' => 'Berlin',
                    'telefonRufnummer' => '+491754968453',
                    'mobilRufnummer' => null,
                    'email' => 'oskarstark@googlemail.com',
                ],
                'makler' => [
                    'person' => [
                        'anrede' => 'Herr',
                        'vorname' => 'Oskar',
                        'nachname' => 'Stark',
                    ],
                    'strasse' => 'Heerstraße 78',
                    'adresszusatz' => null,
                    'plz' => '14055',
                    'ort' => 'Berlin',
                    'telefonRufnummer' => '+491754968453',
                    'mobilRufnummer' => null,
                    'email' => 'oskarstark@googlemail.com',
                ],
            ],
        ];
    }
}
