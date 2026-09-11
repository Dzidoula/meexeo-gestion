<?php
// app/Http/Requests/UpdateTenantRequest.php
namespace App\Http\Requests;

class UpdateTenantRequest extends StoreTenantRequest
{
    // Mêmes règles : l'édition ne doit pas pouvoir enregistrer une fiche incomplète.
}
