<?php
namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Cheque = 'cheque';
    case BankTransfer = 'bank_transfer';
    case Wave = 'wave';
    case OrangeMoney = 'orange_money';
    case MtnMoney = 'mtn_money';
    case MoovMoney = 'moov_money';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Espèces',
            self::Cheque => 'Chèque',
            self::BankTransfer => 'Virement bancaire',
            self::Wave => 'Wave',
            self::OrangeMoney => 'Orange Money',
            self::MtnMoney => 'MTN Money',
            self::MoovMoney => 'Moov Money',
        };
    }

    /** Le libellé du champ de référence change selon le mode ; Espèces n'en a pas. */
    public function referenceLabel(): ?string
    {
        return match ($this) {
            self::Cash => null,
            self::Cheque => 'Numéro de chèque',
            self::BankTransfer => 'Référence du virement',
            self::Wave, self::OrangeMoney, self::MtnMoney, self::MoovMoney => 'Référence de la transaction',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
