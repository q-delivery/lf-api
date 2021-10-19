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
use Gansel\LF\Api\Domain\Value\KfzDarlehen\KfzDarlehenUuid;
use Gansel\LF\Api\Domain\Value\LeadSource;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use function Symfony\Component\String\u;
use Webmozart\Assert\Assert;

final class FallApi extends Api
{
    private const TRANSITION_EINREICHEN = 'einreichen';
    private const TRANSITION_TO_EINGABE_MIT_WARTEN = 'to-eingabe-mit-warten';

    /**
     * @param array<mixed> $payload
     */
    public function create(array $payload): FallUuid
    {
        Assert::notEmpty($payload);

        if (\array_key_exists('leadSource', $payload) && '' === trim($payload['leadSource'])) {
            $payload['leadSource'] = null;
        }

        if (!\array_key_exists('leadSource', $payload)) {
            $payload['leadSource'] = LeadSource::IMMO_Formular()->toString();

            if (\array_key_exists('kfzDarlehen', $payload)) {
                $payload['leadSource'] = LeadSource::AKW_Formular()->toString();
            }
        }

        $response = $this->client->request(
            'POST',
            \Safe\sprintf('%s/faelle', $this->baseUri),
            [
                'json' => $payload,
            ]
        );

        return FallUuid::fromString($response->toArray(true)['id']);
    }

    /**
     * @param array<mixed> $payload
     */
    public function update(FallUuid $fallUuid, array $payload): FallUuid
    {
        Assert::notEmpty($payload);

        $fall = $this->get($fallUuid);
        Assert::notEmpty($fall);
        Assert::keyExists($fall, 'version');

        /*
         * This needs to be done, because Embeddable objects (ORM) needs to be set
         * completely! Otherwise it would empty all existing, and not updated fields!
         */
        if (\array_key_exists('darlehensnehmer', $payload)) {
            $payload['darlehensnehmer'] = array_merge(
                $fall['darlehensnehmer'],
                $payload['darlehensnehmer']
            );
        }

        /*
         * This needs to be done, because Embeddable objects (ORM) needs to be set
         * completely! Otherwise it would empty all existing, and not updated fields!
         */
        if (\array_key_exists('makler', $payload)) {
            $payload['makler'] = array_merge(
                $fall['makler'],
                $payload['makler']
            );
        }

        if (\array_key_exists('kfzDarlehen', $payload)) {
            Assert::keyExists($fall['kfzDarlehen'], 'id');

            $kfzDarlehenUuid = KfzDarlehenUuid::fromString($fall['kfzDarlehen']['id']);
            $payload['kfzDarlehen']['id'] = $kfzDarlehenUuid->toIri();
        }

        $payload['version'] = $fall['version'];

        $response = $this->client->request(
            'PUT',
            \Safe\sprintf(
                '%s/faelle/%s',
                $this->baseUri,
                $fallUuid->toString()
            ),
            [
                'json' => $payload,
            ]
        );

        return FallUuid::fromString($response->toArray(true)['id']);
    }

    /**
     * @param bool               $decision          The decision
     * @param \DateTimeInterface $decisionDate      When the decision was made
     * @param string|null        $contactTime       When is the best time to contact der user
     * @param string|null        $localPhoneNumber  On which local phone number to call the user
     * @param string|null        $mobilePhoneNumber On which mobile phone number to call the user
     */
    public function updateLeadsaleValues(
        FallUuid $fallUuid,
        bool $decision,
        \DateTimeInterface $decisionDate,
        string $contactTime = null,
        string $localPhoneNumber = null,
        string $mobilePhoneNumber = null
    ): FallUuid {
        return $this->update($fallUuid, [
            'leadsaleDecision' => $decision,
            'leadsaleDecisionDate' => $decisionDate->format('Y-m-d H:i:s'),
            'leadsaleContactTime' => $contactTime,
            'leadsaleTelefonRufnummer' => $localPhoneNumber,
            'leadsaleMobilRufnummer' => $mobilePhoneNumber,
        ]);
    }

    /**
     * @param FallUuid    $fallUuid The UUID of the Fall you want to add files to
     * @param string      $filepath The absolute filepath
     * @param string|null $prefix   A prefix which should be added before the filename
     *
     * @return array<int, string>
     */
    public function uploadFile(FallUuid $fallUuid, string $filepath, string $prefix = null, bool $markAsNew = false): array
    {
        Assert::fileExists($filepath);

        $name = null;

        if (null !== $prefix && '' !== trim($prefix)) {
            $name = u(basename($filepath))->ensureStart(\Safe\sprintf('%s ', trim($prefix)))->toString();
        }

        $fields = [
            'files' => [
                DataPart::fromPath($filepath, $name),
            ],
        ];

        $formData = new FormDataPart($fields);

        $parameters = [
            'target' => '07 Aktenanlage',
        ];

        if ($markAsNew) {
            $parameters['mark_as_new'] = 1;
        }

        $response = $this->client->request(
            'POST',
            \Safe\sprintf(
                '%s/faelle/files/upload/%s?%s',
                $this->baseUri,
                $fallUuid->toString(),
                http_build_query($parameters)
            ),
            [
                'headers' => $formData->getPreparedHeaders()->toArray(),
                'body' => $formData->bodyToIterable(),
            ]
        );

        return $response->toArray(true);
    }

    public function markAsFinished(FallUuid $fallUuid): bool
    {
        return $this->applyTransition($fallUuid, self::TRANSITION_EINREICHEN);
    }

    public function markAsWaitingForAdditionalUserData(FallUuid $fallUuid): bool
    {
        return $this->applyTransition($fallUuid, self::TRANSITION_TO_EINGABE_MIT_WARTEN);
    }

    /**
     * @return array<mixed>
     */
    public function get(FallUuid $fallUuid): array
    {
        $response = $this->client->request(
            'GET',
            \Safe\sprintf(
                '%s/faelle/%s',
                $this->baseUri,
                $fallUuid->toString()
            )
        );

        return $response->toArray(true);
    }

    /**
     * @param array<mixed> $payload
     *
     * @return array<mixed>
     */
    public static function modifyPayload(array $payload): array
    {
        if (isset($payload['darlehensnehmer']['person1'])) {
            $additionalData = self::extractAndRemoveData($payload, 'darlehensnehmer');

            $payload['darlehensnehmer']['person1'] = array_merge(
                $payload['darlehensnehmer']['person1'],
                $additionalData
            );

            if (isset($payload['darlehensnehmer']['person2'])) {
                $payload['darlehensnehmer']['person2'] = array_merge(
                    $payload['darlehensnehmer']['person2'],
                    $additionalData
                );
            }
        }

        if (isset($payload['makler']['person'])) {
            $additionalData = self::extractAndRemoveData($payload, 'makler');

            $payload['makler']['person'] = array_merge(
                $payload['makler']['person'],
                $additionalData
            );
        }

        return $payload;
    }

    private function applyTransition(FallUuid $fallUuid, string $transition): bool
    {
        Assert::stringNotEmpty($transition);

        $fall = $this->get($fallUuid);
        Assert::notEmpty($fall);
        Assert::keyExists($fall, 'version');

        $response = $this->client->request(
            'PUT',
            \Safe\sprintf(
                '%s/faelle/%s',
                $this->baseUri,
                $fallUuid->toString()
            ),
            [
                'json' => [
                    'version' => $fall['version'],
                    'statusuebergang' => $transition,
                ],
            ]
        );

        return 200 === $response->getStatusCode() ? true : false;
    }

    /**
     * @param array<mixed> $payload
     *
     * @return array<mixed>
     */
    private static function extractAndRemoveData(array &$payload, string $property): array
    {
        Assert::stringNotEmpty($property);
        Assert::keyExists($payload, $property);

        $keys = [
            'strasse',
            'adresszusatz',
            'plz',
            'ort',
            'telefonRufnummer',
            'mobilRufnummer',
            'email',
        ];

        $additionalData = [];

        foreach ($keys as $key) {
            if (\array_key_exists($key, $payload[$property])) {
                $additionalData[$key] = $payload[$property][$key];
                unset($payload[$property][$key]);
            }
        }

        return $additionalData;
    }
}
