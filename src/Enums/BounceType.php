<?php

namespace Spatie\MailcoachSesFeedback\Enums;

// reference: https://docs.aws.amazon.com/ses/latest/dg/notification-contents.html#bounce-types
enum BounceType: string
{
    case Undetermined = 'Undetermined';
    case Permanent = 'Permanent';
    case Transient = 'Transient';

    public static function softBounces(): array
    {
        return [
            self::Undetermined->value,
            self::Transient->value,
        ];
    }
}
