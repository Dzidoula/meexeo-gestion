<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contrôle des jetons — MEEXEO</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-sable font-ui text-lagune p-8">
    <h1 class="font-titre text-4xl">Contrôle des jetons</h1>
    <p class="text-ardoise">Ardoise</p>
    <p class="text-brume">Brume</p>
    <p class="surtitre">Sur-titre</p>
    <div class="mt-4 rounded-meexeo border border-lin bg-papier p-4">
        <span class="chiffre text-2xl">450 000 FCFA</span>
    </div>
    <div class="mt-4 flex gap-2">
        <span class="rounded-full bg-ok-bg border border-ok-bord text-ok-texte px-2 py-1">Payé</span>
        <span class="rounded-full bg-part-bg border border-part-bord text-part-texte px-2 py-1">Partiel</span>
        <span class="rounded-full bg-retard-bg border border-retard-bord text-retard-texte px-2 py-1">En retard</span>
        <span class="rounded-full bg-impaye-bg border border-impaye-bord text-impaye-texte px-2 py-1">Impayé</span>
        <span class="rounded-full bg-neutre-bg border border-neutre-bord text-neutre-texte px-2 py-1">Libre</span>
    </div>
    <div class="mt-4 flex gap-2">
        <button class="min-h-[44px] rounded-meexeo bg-cuivre px-4 text-papier">Action principale</button>
        <button class="min-h-[44px] rounded-meexeo bg-lagune px-4 text-sable">Action forte</button>
        <button class="min-h-[44px] rounded-meexeo border border-galet bg-papier px-4">Action neutre</button>
        <button class="min-h-[44px] rounded-meexeo px-4 text-terre">Supprimer</button>
    </div>
</body>
</html>
