<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->logAudit('created', 'created the record', [], $model->getAttributes());
        });

        static::updated(function ($model) {
            $oldValues = [];
            $newValues = [];
            $changesDescription = [];

            // Get changed attributes
            foreach ($model->getChanges() as $key => $value) {
                // Ignore timestamps
                if (in_array($key, ['created_at', 'updated_at'])) continue;
                
                $oldValues[$key] = $model->getOriginal($key);
                $newValues[$key] = $value;
                $changesDescription[] = "updated {$key}";
            }

            if (!empty($changesDescription)) {
                $desc = count($changesDescription) > 1 
                    ? "updated multiple fields" 
                    : $changesDescription[0] . " to '{$newValues[array_key_first($newValues)]}'";
                
                $model->logAudit('updated', $desc, $oldValues, $newValues);
            }
        });

        static::deleted(function ($model) {
            $model->logAudit('deleted', 'deleted the record', $model->getAttributes(), []);
        });
    }

    protected function logAudit($action, $description, $oldValues = null, $newValues = null)
    {
        AuditLog::create([
            'auditable_type' => static::class,
            'auditable_id' => $this->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable')->latest();
    }
}
