<?php

namespace App\Console\Commands;

use App\Analytics\BestProductPerCategory;
use App\Analytics\ChurnRisk;
use App\Analytics\FrequentlyBoughtTogether;
use App\Analytics\HighValueCustomers;
use App\Analytics\MonthlyRevenueGrowth;
use App\Analytics\PopularButPoorlyRated;
use App\Analytics\ProductsBelowCategoryAverage;
use App\Analytics\ReturnBehavior;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class RunAnalytics extends Command
{
    protected $signature = 'analytics:run {query}';

    protected $description = 'Run an e-commerce analytical query (q1 to q8)';

    public function handle(): int
    {
        $query = match ($this->argument('query')) {
            'q1' => new HighValueCustomers,
            'q2' => new FrequentlyBoughtTogether,
            'q3' => new ProductsBelowCategoryAverage,
            'q4' => new ChurnRisk,
            'q5' => new MonthlyRevenueGrowth,
            'q6' => new PopularButPoorlyRated,
            'q7' => new ReturnBehavior,
            'q8' => new BestProductPerCategory,
            default => null,
        };

        if ($query === null) {
            $this->error('Choose a query from q1 to q8.');

            return self::INVALID;
        }

        // Exclude connection setup from the query timings.
        DB::connection()->getReadPdo();

        [$rows, $count, $elapsed] = $this->measure($query->run(...));
        if ($rows->isEmpty()) {
            $this->info('No matching rows.');
        } else {
            $this->table(array_keys((array) $rows->first()), $rows->map(fn ($row) => (array) $row)->all());
        }

        $this->line(sprintf('Primary: %d SQL queries, %.2f ms', $count, $elapsed));

        return self::SUCCESS;
    }

    private function measure(callable $run): array
    {
        DB::flushQueryLog();
        DB::enableQueryLog();
        $start = hrtime(true);

        try {
            $rows = $run();

            return [$rows, count(DB::getQueryLog()), (hrtime(true) - $start) / 1_000_000];
        } finally {
            DB::disableQueryLog();
            DB::flushQueryLog();
        }
    }
}
