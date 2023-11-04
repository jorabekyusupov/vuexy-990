<?php

namespace App\Traits;

use Carbon\Carbon;

trait TimeInterval
{

    public function getMonth()
    {
        // last 3 month
        $id = isset(auth()->user()->id) ? auth()->user()->id : 0;
        $carbon_start = !empty(request()->get('start_date')) ? Carbon::createFromFormat('Y-m-d', request()->get('start_date')) : Carbon::now()->subMonths(1);
        $carbon_end = !empty(request()->get('end_date')) ? Carbon::createFromFormat('Y-m-d', request()->get('end_date')) : Carbon::now();

        if ($id === 38946 && (request()->getClientIp() === '127.0.0.1')) {
            $carbon_start = !empty(request()->get('start_date')) ? Carbon::createFromFormat('Y-m-d', request()->get('start_date')) : Carbon::now()->subMonths(4);
            $carbon_end = !empty(request()->get('end_date')) ? Carbon::createFromFormat('Y-m-d', request()->get('end_date')) : Carbon::now();
        }

        if ($carbon_end->format('Y-m-d') == date('Y-m-d')) {
            $carbon_end->addDay();
        }
        $start = $carbon_start->format('Y-m-d');
        $end = $carbon_end->format('Y-m-d');
        if ($start >= $end) {
            $carbon_end = !empty(request()->get('start_date')) ? Carbon::createFromFormat('Y-m-d', request()->get('start_date')) : Carbon::now()->subMonths(1);
            $carbon_end->addDay();
            $end = $carbon_end->format('Y-m-d');
        }
        return [
            $start,
            $end
        ];

    }

}
