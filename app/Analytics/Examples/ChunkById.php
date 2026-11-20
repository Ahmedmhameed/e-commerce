<?php

namespace App\Analytics\Examples;

use App\Models\Order;
use Brick\Math\BigDecimal;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\Collection;

class ChunkById
{
    public function run(DateTimeImmutable $month, int $batchSize = 100): string
    {
        $start = $month->modify('first day of this month')->setTime(0, 0);
        $end = $start->modify('+1 month');
        $total = BigDecimal::zero();

        Order::successful()
            ->select('orders.id', 'orders.total')
            ->where('orders.created_at', '>=', $start)
            ->where('orders.created_at', '<', $end)
            ->chunkById($batchSize, function (Collection $orders) use (&$total): void {
                $batchTotal = BigDecimal::zero();

                foreach ($orders as $order) {
                    $batchTotal = $batchTotal->plus((string) $order->total);
                }

                $total = $total->plus($batchTotal);
            }, 'orders.id', 'id');

        return (string) $total->toScale(2);
    }
}
