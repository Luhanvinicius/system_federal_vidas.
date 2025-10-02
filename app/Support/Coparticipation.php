<?php

namespace App\Support;

class Coparticipation
{
    /**
     * Tenta resolver o valor de coparticipação a partir de um modelo de Specialty,
     * cobrindo vários nomes de coluna possíveis.
     */
    public static function resolveFromSpecialty($specialty): ?float
    {
        if (!$specialty) return null;

        $candidates = [
            'coparticipation_price',
            'coparticipation',
            'co_participation',
            'co_participation_price',
            'price',
            'value',
            'fee',
        ];

        foreach ($candidates as $attr) {
            if (isset($specialty->{$attr}) && $specialty->{$attr} !== null && $specialty->{$attr} !== '') {
                return (float) str_replace(',', '.', (string) $specialty->{$attr});
            }
        }
        return null;
    }

    /** Normaliza string/float de "R$ 40,00" para 40.00 */
    public static function normalize($value): ?float
    {
        if ($value === null || $value === '') return null;
        if (is_numeric($value)) return (float) $value;
        $norm = str_replace(['R$', ' ', '.'], '', (string) $value); // remove milhares e símbolo
        $norm = str_replace(',', '.', $norm);
        return is_numeric($norm) ? (float) $norm : null;
    }
}
