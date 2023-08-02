<?php

namespace App\Console\Commands;
use Carbon\Carbon;
use App\Promotion;

use Illuminate\Console\Command;

class endPromotions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'end:promotions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'deletes the promotions table data after 7days of creation to end promotions';

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
        $date = new Carbon;
        $date->subDays(7);
		$ifWeek = Promotion::where('created_at', '>', $date->toDateTimeString())->first();
        if($ifWeek)
        {
            Promotion::truncate();
		}
    }
}
