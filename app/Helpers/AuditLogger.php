<?php

namespace App\Helpers;

use App\Models\AuditLog;

class AuditLogger
{
    const CREATED = 'created';
    const UPDATED = 'updated';
    const DELETED = 'deleted';

    public function __construct(protected int $actorId, protected string $description)
    {}

    public function logAsCreated() : void
    {
        $this->createLog(self::CREATED);
    }

    public function logAsUpdated() : void
    {
        $this->createLog(self::UPDATED);
    }

    public function logAsDelete() : void
    {
        $this->createLog(self::DELETED);
    }

    private function createLog(string $type) : void
    {
        AuditLog::create([
            'user_id' => $this->actorId,
            'action' => $type,
            'description' => $this->description
        ]);
    }
}
