<?php

namespace App\Enums;

enum StatusAktifEnum: string
{
    case AKTIF = 'aktif';
    case NONAKTIF = 'nonaktif';

    public function label(): string
    {
        return match ($this) {
            self::AKTIF => 'Aktif',
            self::NONAKTIF => 'Nonaktif',
        };
    }

    public static function values(): array
    {
        return array_map(
            static fn (self $status) => $status->value,
            self::cases()
        );
    }

    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $status) {
            $options[$status->value] = $status->label();
        }

        return $options;
    }

    public static function labelOf(?string $value): string
    {
        return self::tryFrom((string) $value)?->label() ?? ucfirst((string) $value);
    }
}
