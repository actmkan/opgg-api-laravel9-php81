<?php

namespace Database\Seeders;

use App\Enums\ChannelPermissionEnum;
use App\Enums\GradeEnum;
use App\Models\ChannelPermission;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (GradeEnum::cases() as $gradeEnum){
            $grade = new Grade();
            $grade->name = $gradeEnum->name();
            $grade->display_name = $gradeEnum->displayName();
            $grade->point = $gradeEnum->point();
            $grade->save();
        }
    }
}
