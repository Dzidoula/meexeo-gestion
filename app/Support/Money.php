<?php
// app/Support/Money.php
namespace App\Support;

class Money
{
    /** Le franc CFA n'a pas de décimales : l'entrée est un entier de francs. */
    public static function fcfa(int $amount): string
    {
        return number_format($amount, 0, ',', ' ').' FCFA';
    }
}
