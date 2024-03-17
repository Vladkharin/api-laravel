<?php

namespace App\Policies;

use App\Models\File;
use App\Models\FileAccess;
use App\Models\User;

class FilePolicy
{
    /**
     * Create a new policy instance.
     */
    public function manage(User $user, File $file)
    {
        return $user->id == $file->user_id;
    }

    public function view(User $user, File $file)
    {
        return $this->manage($user, $file) || FileAccess::query()->where('user_id', $user->id)->where('file_id', $file->id)->exists();
    }
}
