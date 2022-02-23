<?php

namespace Database\Seeders;

use App\Enums\ChannelPermissionEnum;
use App\Enums\GradeEnum;
use App\Models\User;
use App\Models\UserPoint;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        foreach (GradeEnum::cases() as $gradeEnum){
            $point = $gradeEnum->point();

            $attr = [
                'grade_id' => $gradeEnum->id(),
                'point' => $point ?? 0
            ];

            $attr['email'] = "{$gradeEnum->name()}@op.gg";
            $attr['nickname'] = $gradeEnum->displayName();

            User::factory()->create($attr);

            //포인트 로그 등록
            if($point){
                UserPoint::factory()->create([
                    'user_id' => $gradeEnum->id(),
                    'point' => $gradeEnum->point(),
                    'type' => 'init'
                ]);
            }
        }
    }
}
