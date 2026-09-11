<?php
// app/Enums/TenantDocumentType.php
namespace App\Enums;

enum TenantDocumentType: string
{
    case IdCard = 'id_card';
    case WorkContract = 'work_contract';
    case Payslip = 'payslip';
    case SignedLease = 'signed_lease';
    case Insurance = 'insurance';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::IdCard => 'CNI / Passeport',
            self::WorkContract => 'Contrat ou attestation de travail',
            self::Payslip => 'Bulletin de salaire',
            self::SignedLease => 'Contrat de bail signé',
            self::Insurance => 'Assurance',
            self::Other => 'Autre document',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
