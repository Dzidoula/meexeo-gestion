<?php
namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Mirrors a create/update/delete on a Touvalem-synced content model back to
 * residencetouvalem.com, best-effort. Identity rule: a row that originated
 * on Touvalem (external_source=residence_touvalem) is addressed there by its
 * real Touvalem primary key (external_id); a row native to MASTERCLAYS is
 * addressed by (external_source=masterclays, external_id=<our own id>) on
 * Touvalem's mirror columns.
 */
class TouvalemSyncPusher
{
    public function push(string $resource, Model $model, array $fields): void
    {
        $this->send("https://residencetouvalem.com/api/masterclays-sync/{$resource}", $this->identity($model) + $fields, $resource);
    }

    public function pushDelete(string $resource, Model $model): void
    {
        $this->send("https://residencetouvalem.com/api/masterclays-sync/{$resource}/delete", $this->identity($model), $resource);
    }

    private function identity(Model $model): array
    {
        return $model->external_source === 'residence_touvalem'
            ? ['touvalem_id' => $model->external_id]
            : ['masterclays_id' => $model->id];
    }

    private function send(string $url, array $payload, string $resource): void
    {
        $token = config('services.touvalem_sync.token');
        if (!$token) {
            return;
        }

        try {
            Http::timeout(5)->withHeader('X-Sync-Token', $token)->post($url, $payload)->throw();
        } catch (\Throwable $e) {
            Log::warning("Échec de la synchronisation {$resource} vers Touvalem.", ['error' => $e->getMessage()]);
        }
    }
}
