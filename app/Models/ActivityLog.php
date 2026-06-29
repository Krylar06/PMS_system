<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'subject_type',
        'subject_id',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record an activity log entry.
     *
     * Usage: ActivityLog::record('created', 'Created college "College of Science"', $college);
     */
    public static function record(string $action, string $description, $subject = null): self
    {
        $user = auth()->user();

        return self::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'action' => $action,
            'subject_type' => $subject ? class_basename($subject) : null,
            'subject_id' => $subject?->id,
            'description' => $description,
        ]);
    }
}