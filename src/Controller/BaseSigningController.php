<?php

declare(strict_types=1);

namespace DBP\API\ESignBundle\Controller;

use DBP\API\CoreBundle\Exception\ApiError;
use DBP\API\CoreBundle\Helpers\JsonException;
use DBP\API\CoreBundle\Helpers\Tools as CoreTools;
use DBP\API\ESignBundle\Service\UserDefinedText;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseSigningController extends AbstractController
{
    /**
     * @return UserDefinedText[]
     */
    public function parseUserText(string $data): array
    {
        // Parse and validate the basics
        try {
            $parsed = CoreTools::decodeJSON($data, true);
        } catch (JsonException $e) {
            throw new ApiError(Response::HTTP_BAD_REQUEST, 'invalid JSON');
        }
        if (!$parsed instanceof \Traversable) {
            throw new ApiError(Response::HTTP_BAD_REQUEST, 'invalid content');
        }
        foreach ($parsed as $entry) {
            if (!is_array($entry) || ($entry['description'] ?? '') === '' || ($entry['value'] ?? '') === '') {
                throw new ApiError(Response::HTTP_BAD_REQUEST, 'invalid content');
            }
        }

        $userText = [];
        foreach ($parsed as $entry) {
            $description = $entry['description'];
            $value = $entry['value'];
            $userText[] = new UserDefinedText($description, $value);
        }

        return $userText;
    }
}