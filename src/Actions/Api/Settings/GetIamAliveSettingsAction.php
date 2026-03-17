<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Settings;

use JsonException;
use Uplinkr\Storage\FileSettingsStorage;

class GetIamAliveSettingsAction
{
    public function __construct(
        private readonly FileSettingsStorage $settingsStorage,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function execute(): array
    {
        return [
            'data' => $this->settingsStorage->getIamAliveSettings(),
        ];
    }
}
