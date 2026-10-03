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

];
