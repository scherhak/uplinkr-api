<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Settings;

use JsonException;
use Uplinkr\Storage\FileSettingsStorage;

/**
 * Class GetIamAliveSettingsAction
 * @package UplinkrApi\Actions\Api\Settings
 *
 * Reads the current global I-am-alive settings from the base package storage.
 *
 * @author Sascha Scherhak <sascha@uplinkr.dev>
 */
class GetIamAliveSettingsAction
{
    /**
     * Initializes the action with the settings storage dependency.
     *
     * @param FileSettingsStorage $settingsStorage The storage used to read global Uplinkr settings.
     * @return void
     */
    public function __construct(
        private readonly FileSettingsStorage $settingsStorage,
    )
    {
    }

    /**
     * Returns the current I-am-alive settings as a normalized API payload.
     *
     * @return array The normalized response payload containing the current settings.
     * @throws JsonException
     */
    public function execute(): array
    {
        return [
            'data' => $this->settingsStorage->getIamAliveSettings(),
        ];
    }
}
