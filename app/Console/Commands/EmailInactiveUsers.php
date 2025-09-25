<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\User;
use App\UserSubscription;
use App\Notifications\SubscriptionExpiringNotification;
use Illuminate\Support\Facades\Auth;
use DB;

class EmailInactiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'command:name';
    protected $signature = 'email:inactive-users';

   /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Command description';
    protected $description = 'Email inactive users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $inactive_user = User::with('getActiveSubscription')->whereHas('getActiveSubscription',function($q){
                $q->where('status','=','1');
              })->get();
        foreach ($inactive_user as $key => $value) {
            $todayDate = date("Y-m-d");
            $daystominus = '7';
            $data = date('Y-m-d', strtotime($value->getActiveSubscription->validity.' - '.$daystominus.' days'));
            if($value->getActiveSubscription->is_expire_email_sent == 0)
            {
                if($todayDate >= $data)
                {
                    $mail_send = $value->notify(new SubscriptionExpiringNotification());
                    UserSubscription::where('user_id',$value->getActiveSubscription->user_id)
                                    ->update(['is_expire_email_sent' => 1]);
                }
            }
                if($todayDate == $value->getActiveSubscription->validity){
                    DB::table('users')
                            ->join('user_subscriptions','user_subscriptions.user_id','=','users.id')
                            ->join('companies','companies.tbl_users_id','=','users.id')
                            ->where('users.id',$value->getActiveSubscription->user_id)
                            ->where('user_subscriptions.user_id',$value->getActiveSubscription->user_id)
                            ->where('companies.tbl_users_id',$value->getActiveSubscription->user_id)
                            ->update([
                            'users.status' => 0,
                            'user_subscriptions.status' => 0,
                            'companies.subscription_id' =>NULL
                    ]);
                }
            }
        // \Log::info("Mail Sent Successfully");

        // $this->info('email:inactive-users command run succesfully!!');
    }
}
