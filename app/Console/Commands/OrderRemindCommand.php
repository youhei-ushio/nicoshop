<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contexts\Sales\Application\UseCase\Order\Remind\Interactor;
use App\Contexts\Sales\Domain\Event\OrderNotYetAccepted;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use Spatie\SlackAlerts\Facades\SlackAlert;

final class OrderRemindCommand extends Command
{
    protected $signature = 'order:remind';

    protected $description = 'Remind not accepted orders.';

    public function handle(Interactor $interactor): int
    {
        $subscriber = new class()
        {
            /** @var OrderNotYetAccepted[] */
            public array $events = [];

            public function __invoke(OrderNotYetAccepted $event): void
            {
                $this->events[] = $event;
            }

            public function notify(): void
            {
                if (empty($this->events)) {
                    return;
                }
                $env = app()->environment();
                $message = "Order not accepted yet.\n*Env*: $env\n";
                foreach ($this->events as $event) {
                    $message .= "*ID*: $event->id\n";
                }
                SlackAlert::message($message);
            }
        };
        Event::listen(
            OrderNotYetAccepted::class,
            $subscriber,
        );
        $interactor->execute();
        $subscriber->notify();
        return parent::SUCCESS;
    }
}
