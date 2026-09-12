<?php

namespace App\Enums;

enum ConnectionStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case PaymentPending = 'payment_pending';
    case Connected = 'connected';
    case Declined = 'declined';
    case Cancelled = 'cancelled';

    /**
     * Statuses that represent an active/in-progress connection lifecycle.
     *
     * @return array<self>
     */
    public static function active(): array
    {
        return [
            self::Pending,
            self::Accepted,
            self::PaymentPending,
            self::Connected,
        ];
    }

    /**
     * Statuses that represent a terminated connection lifecycle.
     *
     * @return array<self>
     */
    public static function terminal(): array
    {
        return [
            self::Declined,
            self::Cancelled,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Accepted => 'Accepted',
            self::PaymentPending => 'Payment Pending',
            self::Connected => 'Connected',
            self::Declined => 'Declined',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * Can this status transition to the given target status?
     */
    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::Pending => in_array($target, [self::Accepted, self::Declined, self::Cancelled]),
            self::Accepted => in_array($target, [self::PaymentPending, self::Cancelled]),
            self::PaymentPending => in_array($target, [self::Connected, self::Cancelled]),
            self::Connected, self::Declined, self::Cancelled => false,
        };
    }
}
