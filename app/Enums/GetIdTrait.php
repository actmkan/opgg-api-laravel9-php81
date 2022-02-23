<?php

namespace App\Enums;

trait GetIdTrait
{
    public function id(): int|null
    {
        foreach (self::cases() as $id => $case){
            if($case === $this) return $id + 1;
        }

        return null;
    }
}
