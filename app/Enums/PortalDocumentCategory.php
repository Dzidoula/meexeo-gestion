<?php
// app/Enums/PortalDocumentCategory.php
namespace App\Enums;

/**
 * Documents publiés AU locataire dans son portail (quittances, bail signé…).
 * À ne pas confondre avec TenantDocumentType, qui couvre les pièces
 * justificatives collectées PAR le gestionnaire (CNI, bulletins de salaire).
 */
enum PortalDocumentCategory: string
{
    case Lease     = 'bail';
    case Receipt   = 'quittance';
    case Invoice   = 'facture';
    case Insurance = 'assurance';
    case Other     = 'autre';

    public function label(): string
    {
        return match ($this) {
            self::Lease     => 'Bail',
            self::Receipt   => 'Quittance',
            self::Invoice   => 'Facture',
            self::Insurance => 'Assurance',
            self::Other     => 'Autre',
        };
    }

    /** Classes de la pastille, reprises telles quelles de la maquette. */
    public function tone(): string
    {
        return match ($this) {
            self::Lease     => 'bg-pl-50 text-pl-600',
            self::Receipt   => 'bg-emerald-50 text-emerald-600',
            self::Invoice   => 'bg-amber-50 text-amber-600',
            self::Insurance => 'bg-blue-50 text-blue-600',
            self::Other     => 'bg-gray-100 text-gray-600',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Lease     => 'file-check',
            self::Receipt   => 'receipt',
            self::Invoice   => 'file-spreadsheet',
            self::Insurance => 'shield',
            self::Other     => 'file-text',
        };
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
