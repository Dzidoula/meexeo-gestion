<?php
// app/Http/Requests/UpdatePropertyRequest.php
namespace App\Http\Requests;

class UpdatePropertyRequest extends StorePropertyRequest
{
    // Mêmes règles que la création : un bien incomplet ne doit pas pouvoir
    // être enregistré par la porte de l'édition.
}
