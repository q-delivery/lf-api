<?php

declare(strict_types=1);

namespace Gansel\LF\Api;

use Gansel\LF\Api\Domain\Value\Fall\FallUuid;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use function Symfony\Component\String\u;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Webmozart\Assert\Assert;

final class FallApi
{
    private $baseUri;
    private $client;

    public function __construct(string $baseUri, HttpClientInterface $client)
    {
        Assert::stringNotEmpty($baseUri);

        if (u($baseUri)->trim()->endsWith('/')) {
            throw new \InvalidArgumentException('$baseUrl should not end with a slash.');
        }

        $this->baseUri = $baseUri;
        $this->client = $client;
    }

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
}
