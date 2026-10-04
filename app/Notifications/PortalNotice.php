<?php

namespace App\Notifications;

use App\Models\Payment;
use App\Models\RepairRequest;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;

/**
 * Notification en base destinée au locataire (cahier des charges, §11).
 * Un seul type paramétré plutôt que huit classes : les événements ne
 * diffèrent que par leur texte, leur catégorie d'icône et leur lien.
 * Les canaux SMS / push viendront plus tard sans toucher à ces textes.
 */
class PortalNotice extends Notification
{
    /**
     * @param  string  $kind  payment | maintenance | rent — choisit l'icône côté vue
     * @param  string|null  $key  identifiant d'événement pour ne pas notifier deux fois
     */
    public function __construct(
        public string $kind,
        public string $title,
        public string $body,
        public ?string $url = null,
        public ?string $key = null,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, string|null> */
    public function toArray(object $notifiable): array
    {
        return [
            'kind'  => $this->kind,
            'title' => $this->title,
            'body'  => $this->body,
            'url'   => $this->url,
            'key'   => $this->key,
        ];
    }

    public static function proofReceived(Payment $payment): self
    {
        return new self('payment', 'Preuve reçue', 'Votre preuve est en cours de vérification.', route('tenant-portal.payments'));
    }

    public static function proofApproved(Payment $payment): self
    {
        return new self('payment', 'Paiement validé', 'Votre paiement a été validé.', route('tenant-portal.payments'));
    }

    public static function proofRejected(string $reason): self
    {
        return new self('payment', 'Preuve rejetée', "Votre preuve a été rejetée : ".rtrim($reason, ' .').'.', route('tenant-portal.payments'));
    }

    public static function repairReceived(RepairRequest $repair): self
    {
        return new self('maintenance', 'Signalement enregistré', "Votre signalement #{$repair->ticket_no} a été enregistré.", route('tenant-portal.repairs.show', $repair));
    }

    public static function repairInProgress(RepairRequest $repair): self
    {
        return new self('maintenance', 'Réparation en cours', "Votre réparation #{$repair->ticket_no} est maintenant en cours.", route('tenant-portal.repairs.show', $repair));
    }

    public static function repairClosed(RepairRequest $repair): self
    {
        return new self('maintenance', 'Réparation clôturée', "Votre réparation #{$repair->ticket_no} a été clôturée.", route('tenant-portal.repairs.show', $repair));
    }

    public static function rentDue(Carbon $due, int $daysAhead): self
    {
        $month = $due->format('Y-m');

        return $daysAhead === 0
            ? new self('rent', 'Loyer dû aujourd\'hui', 'Votre loyer est dû aujourd\'hui.', route('tenant-portal.payments'), "rent-due:{$month}:0")
            : new self('rent', 'Échéance proche', "Votre loyer arrive à échéance dans {$daysAhead} jours.", route('tenant-portal.payments'), "rent-due:{$month}:{$daysAhead}");
    }
}
