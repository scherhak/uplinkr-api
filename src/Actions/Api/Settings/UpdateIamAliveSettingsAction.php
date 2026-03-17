<?php

declare(strict_types=1);

namespace UplinkrApi\Actions\Api\Settings;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JsonException;
use Uplinkr\Storage\FileSettingsStorage;

class UpdateIamAliveSettingsAction
{
    public function __construct(
        private readonly FileSettingsStorage $settingsStorage,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function execute(Request $request): array
    {
        $data = Validator::make($request->all(), [
            'enabled' => ['sometimes', 'boolean'],
            'interval_hours' => ['sometimes', 'integer', 'min:1', 'max:24'],
            'channels' => ['sometimes', 'array', 'min:1'],
            'channels.*' => ['string', 'in:mail,log,webhook'],
        ])->validate();

        if ($data === []) {
            return [
                'status' => 422,
                'data' => null,
                'meta' => [
                    'message' => 'At least one settings field must be provided.',
                ],
            ];
        }

        $enabled = array_key_exists('enabled', $data) ? (bool) $data['enabled'] : null;
        $intervalHours = array_key_exists('interval_hours', $data) ? (int) $data['interval_hours'] : null;
        $channels = array_key_exists('channels', $data) ? array_values(array_unique($data['channels'])) : null;

        if ($enabled === null && ($intervalHours !== null || $channels !== null)) {
            $enabled = true;
        }

        return [
            'data' => $this->settingsStorage->updateIamAliveSettings($enabled, $intervalHours, $channels),
        ];
    }
}
