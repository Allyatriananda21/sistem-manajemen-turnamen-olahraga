@props([
    'status',
    'type'  => 'match',
    'size'  => 'sm',
])

@php
    /**
     * Reusable status badge component.
     *
     * Supports two domains:
     *   type="match"  → scheduled | ongoing | done | cancelled
     *   type="team"   → pending | approved | disqualified
     *
     * @param string $status  The status value to display.
     * @param string $type    'match' (default) | 'team'
     * @param string $size    Flux badge size: 'sm' (default) | 'lg' etc.
     *
     * Additional attributes (class, etc.) are forwarded to flux:badge via $attributes.
     *
     * Usage:
     *   <x-status-badge :status="$match->status" />
     *   <x-status-badge :status="$match->status" size="lg" class="self-start shrink-0" />
     *   <x-status-badge :status="$team->status" type="team" />
     *   <x-status-badge :status="$team->status" type="team" size="lg" />
     */

    $config = match ($type) {
        'team' => [
            'pending'      => ['color' => 'zinc',  'label' => 'Pending'],
            'approved'     => ['color' => 'green', 'label' => 'Approved'],
            'disqualified' => ['color' => 'red',   'label' => 'Disqualified'],
        ],
        default => [ // match
            'scheduled' => ['color' => 'zinc',   'label' => 'Scheduled'],
            'ongoing'   => ['color' => 'yellow', 'label' => 'Ongoing'],
            'done'      => ['color' => 'green',  'label' => 'Done'],
            'cancelled' => ['color' => 'red',    'label' => 'Cancelled'],
        ],
    };

    $entry = $config[$status] ?? ['color' => 'zinc', 'label' => ucfirst($status)];
@endphp

<flux:badge :color="$entry['color']" :size="$size" {{ $attributes }}>{{ $entry['label'] }}</flux:badge>
