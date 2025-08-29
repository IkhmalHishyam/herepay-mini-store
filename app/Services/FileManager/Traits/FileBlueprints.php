<?php

namespace App\Services\FileManager\Traits;

use App\Models\User;
use App\Services\FileManager\Enums\FileAccessLevelEnum;

trait FileBlueprints
{
    public static function getPredefinedBlueprint(
        ?User $user,
        FileAccessLevelEnum $access_level,
        string $base_model,
        string $model_id,
        string $fileable,
    ): string {
        $tier_0 = $user ? class_basename($user).DIRECTORY_SEPARATOR : '';
        $tier_1 = $user ? $user->id.DIRECTORY_SEPARATOR : '';
        $tier_2 = $access_level->value.DIRECTORY_SEPARATOR;
        $tier_3 = $base_model.DIRECTORY_SEPARATOR;
        $tier_4 = $model_id.DIRECTORY_SEPARATOR;
        $tier_5 = $fileable.DIRECTORY_SEPARATOR;

        return $tier_0.$tier_1.$tier_2.$tier_3.$tier_4.$tier_5;
    }

    public static function getCustomBlueprint(
        array $custom_blueprint
    ): string {
        $blueprint = '';

        foreach ($custom_blueprint as $key => $value)
        {
            $blueprint .= $value.DIRECTORY_SEPARATOR;
        }

        return $blueprint;
    }
}
