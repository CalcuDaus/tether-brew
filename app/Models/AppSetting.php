<?php

namespace App\Models;

use App\Models\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    use BelongsToBranch;

    protected $fillable = ['key', 'value', 'label', 'branch_id'];

    /**
     * Get a setting value by key for the active branch, with optional default.
     */
    public static function getValue(string $key, $default = null, $branchId = null)
    {
        $query = static::where('key', $key);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $setting = $query->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key for a specific branch.
     */
    public static function setValue(string $key, $value, $branchId = null): void
    {
        $conditions = ['key' => $key];
        if ($branchId) {
            $conditions['branch_id'] = $branchId;
        }

        static::updateOrCreate($conditions, ['value' => $value]);
    }
}
