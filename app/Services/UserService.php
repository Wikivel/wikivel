<?php

namespace App\Services;

use App\Models\User;

class UserService extends ModelService
{
    public function __construct()
    {
        $this->className = User::class;
    }

    /**
     * Get a model instance by ID. Models must have the $className property set
     * via a construct method for this to work.
     *
     * @param string|array|mixed $modelUsernames
     * @return \Illuminate\Support\Collection|mixed
     */
    public function getByUsername($modelUsernames)
    {
        if (!is_iterable($modelUsernames)) {
            $modelUsernames = [$modelUsernames];
        }

        $modelRecords = $this->className::whereIn('username', $modelUsernames)->get();

        if ($modelRecords->count() > 1) {
            return $modelRecords;
        }

        return $modelRecords->first();
    }
}
