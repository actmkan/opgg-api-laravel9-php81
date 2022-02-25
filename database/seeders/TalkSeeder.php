<?php

namespace Database\Seeders;

use App\Enums\ChannelPermissionEnum;
use App\Enums\GradeEnum;
use App\Enums\TalkEnum;
use App\Models\Article;
use App\Models\Channel;
use App\Models\ChannelPermission;
use App\Models\Comment;
use App\Models\Talk;
use App\Models\User;
use App\Services\AuthService;
use App\Services\ImageService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TalkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function run()
    {
        $imageService = new ImageService();
        $admin = User::where(['grade_id' => GradeEnum::ADMIN->id()])->first();

        foreach (TalkEnum::cases() as $talkEnum){
            $talk = new Talk();
            $talk->name = $talkEnum->name();
            $talk->display_name = $talkEnum->displayName();
            $talk->banner_image_id = $imageService->upload(['file' => new UploadedFile($talkEnum->bannerImage(), pathinfo($talkEnum->bannerImage(), PATHINFO_FILENAME)), 'type' => 'banner'], true)->id;
            $talk->logo_image_id = $imageService->upload(['file' => new UploadedFile($talkEnum->logoImage(), pathinfo($talkEnum->logoImage(), PATHINFO_FILENAME)), 'type' => 'logo'], true)->id;
            $talk->background_image_id = $imageService->upload(['file' => new UploadedFile($talkEnum->backgroundImage(), pathinfo($talkEnum->backgroundImage(), PATHINFO_FILENAME)), 'type' => 'bg'], true)->id;
            $talk->save();
            dump($talkEnum->displayName() . " 톡 생성 완료");

            /** @var \App\Enums\ChannelEnum $channelEnum */
            foreach ($talkEnum->channels() as $channelEnum){
                $channel = new Channel();
                $channel->talk_id = $talk->id;
                $channel->group = $channelEnum->group();
                $channel->display_group = $channelEnum->displayGroup();
                $channel->name = $channelEnum->name();
                $channel->display_name = $channelEnum->displayName();
                $channel->save();
                dump($channelEnum->displayName() . " 채널 생성 완료");

                foreach ($channelEnum->permissions() as $permissionArr){
                    $permissionType = $permissionArr[0]->name;
                    $graceId = $permissionArr[1] === null ? $permissionArr[1] : $permissionArr[1]->id();
                    $isWriter = $permissionArr[2];
                    $channelPermission = new ChannelPermission();
                    $channelPermission->channel_id = $channel->id;
                    $channelPermission->type = $permissionType;
                    $channelPermission->grade_id = $graceId;
                    $channelPermission->is_writer = $isWriter;
                    $channelPermission->save();
                    dump($channelEnum->displayName() . " 채널 " . $permissionType . " 생성 완료");
                }

                //notice 작성 2건
                Article::factory(2)->create([
                    'is_notice' => true,
                    'is_all_notice' => random_int(0, 9) === 9,
                    'talk_id' => $talk->id,
                    'channel_id' => $channel->id,
                    'user_id' => $admin->id,
                ]);

                dump($channelEnum->displayName() . " 채널 공지사항 생성 완료");
                //글작성 100건
                Article::factory(30)->create([
                    'is_notice' => false,
                    'is_all_notice' => false,
                    'talk_id' => $talk->id,
                    'channel_id' => $channel->id,
                ]);
                dump($channelEnum->displayName() . " 작성글 생성 완료");
            }
        }

        //댓글 작성
        foreach (Article::all() as $article){
            Comment::factory(random_int(0, 10))->create([
                'article_id' => $article->id
            ]);
            dump($article->id . " 댓글 생성 완료");
        }

        //답글 작성
        foreach (Comment::all() as $comment){
            Comment::factory(random_int(0, 3))->create([
                'article_id' => $comment->article_id,
                'parent_id' => $comment->id,
            ]);
            dump($comment->id . " 답글 생성 완료");
        }
    }
}
