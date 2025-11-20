<?php

namespace App\Filament\Index\Traits;

trait BlacklistedWordsTrait
{
    protected array $blacklist = ['bannedword1', 'bannedword2'];

    public function containsBlacklistedWords(): bool
    {
        foreach ($this->getBlacklistedFields() as $field) {
            if (isset($this->$field) && stripos($this->$field, $this->blacklist[0]) !== false) {
                return true;
            }
        }

        return false;
    }
}
