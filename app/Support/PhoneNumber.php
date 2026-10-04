<?php

namespace App\Support;

class PhoneNumber
{
    private const CI_COUNTRY_CODE = '225';

    /**
     * Normalise un numéro ivoirien vers +225XXXXXXXXXX, quel que soit le format
     * saisi (espaces, tirets, indicatif présent ou non, 00 international).
     * Un locataire tape seulement son numéro local ; c'est ce format canonique
     * qui est stocké et qui sert de clé de recherche à la connexion — les deux
     * passent par cette même fonction pour ne jamais diverger.
     */
    public static function ivoirianE164(string $raw): string
    {
        $digits = preg_replace('/\D+/', '', $raw);

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '00'.self::CI_COUNTRY_CODE)) {
            $digits = substr($digits, 2);
        } elseif (! str_starts_with($digits, self::CI_COUNTRY_CODE)) {
            $digits = self::CI_COUNTRY_CODE.$digits;
        }

        return '+'.$digits;
    }
}
