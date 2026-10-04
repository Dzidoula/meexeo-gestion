<?php

return [

    /*
    |---------------------------------------------------------------------------
    | Show the OTP code on screen
    |---------------------------------------------------------------------------
    |
    | Displays the login code directly on the verification page instead of
    | sending it by SMS. This exists so the portal can be demonstrated before
    | an SMS provider is wired up.
    |
    | Anyone who knows a tenant's phone number can sign in as that tenant while
    | this is on. It is opt-in for that reason: a real deployment leaves
    | TENANT_PORTAL_SHOW_OTP unset and is safe by default.
    |
    */

    'show_otp_on_screen' => env('TENANT_PORTAL_SHOW_OTP', false),

    /*
    |---------------------------------------------------------------------------
    | Dedicated tenant hostname
    |---------------------------------------------------------------------------
    |
    | admin.masterclays.net, locataire.masterclays.net and masterclays.net are
    | the same Apache vhost and the same Laravel install — Laravel's router does
    | not look at the Host header, so every one of them renders the same "/"
    | route. Visiting the tenant-branded hostname must not land a tenant on the
    | public vehicle storefront, so the root route checks this value and
    | redirects into the portal when it matches.
    |
    */

    'tenant_host' => env('TENANT_PORTAL_HOST', 'locataire.masterclays.net'),

    /*
    |---------------------------------------------------------------------------
    | Staff hostname
    |---------------------------------------------------------------------------
    |
    | Same sharing problem as tenant_host, for the staff-facing name: visiting
    | admin.masterclays.net must enter the admin flow (dashboard, or login for
    | a guest) rather than the public storefront that masterclays.net itself
    | legitimately shows.
    |
    */

    'admin_host' => env('ADMIN_PORTAL_HOST', 'admin.masterclays.net'),

];
