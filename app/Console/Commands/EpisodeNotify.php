<?php

namespace App\Console\Commands;

use App\Models\Episode;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EpisodeNotify extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:episodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();

        foreach ($users as $user) {
            $episodes = Episode::where('status', '1')->where('appointment', '<=', Carbon::now())
            ->where('send_notification', '1')
            ->whereNotExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                    ->from('notifications')
                    ->whereRaw('notifications.product_id = episodes.id')
                    ->where('notifications.page', 'episodes')
                    ->where('notifications.user_id', $user->id);
            })
            ->get();
            if($episodes->count() > 0) {
                foreach($episodes as $episode) {
                    Notification::create([
                        'user_id' => $user->id,
                        'message' => 'يوجد حلقة جديده '. $episode->name,
                        'page' => 'episodes',
                        'product_name' => $episode->name,
                        'product_id' => $episode->id,
                    ]);
                }
            }
        }
    }
}
