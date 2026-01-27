<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportPppCustomersRequested
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param int $operatorId The operator initiating the import
     * @param int|null $nasId The NAS device ID (nullable for backward compatibility; router_id can be passed via options)
     * @param array $options Additional options including router_id, filter_disabled, generate_bills, package_id
     */
    public function __construct(
        public int $operatorId,
        public ?int $nasId,
        public array $options = []
    ) {}

    /**
     * Get import options.
     */
    public function getOptions(): array
    {
        return array_merge([
            'router_id' => null,
            'filter_disabled' => true,
            'generate_bills' => false,
            'package_id' => null,
        ], $this->options);
    }
}
