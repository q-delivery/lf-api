<?php

declare(strict_types=1);

namespace Gansel\LF\Api;

use Gansel\LF\Api\Domain\Value\Fall\FallUuid;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Webmozart\Assert\Assert;

final class FallApi extends Api
{
    /**
     * @param array<mixed> $payload
     */
    public function add(array $payload): FallUuid
    {
        Assert::notEmpty($payload);

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
     * @param FallUuid $fallUuid The UUID of the Fall you want to add files to
     * @param string   $filepath The absolute filepath
     *
     * @return array<int, string>
     */
    public function uploadFile(FallUuid $fallUuid, string $filepath, bool $markAsNew = false): array
    {
        Assert::fileExists($filepath);

        $fields = [
            'files' => [
                DataPart::fromPath($filepath),
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

    public function applyTransition(FallUuid $fallUuid, string $transition): bool
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
            ),
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
            'addresszusatz',
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
