# Déploiement production — admin.masterclays.net

Ce document décrit comment MEEXEO Immobilier a été mis en production, pour pouvoir reproduire ou déboguer le déploiement plus tard.

## Infrastructure

- **Serveur** : VPS partagé Master Clays (ISPConfig 3), `180.149.197.201`.
- **Domaine** : `admin.masterclays.net` (DNS géré côté client chez LWS — enregistrement `A` pointant vers l'IP ci-dessus).
- **Site ISPConfig** : client `MASTERCLAYS` (client_id 7), site id 57, utilisateur système `web57`, racine `/var/www/clients/client7/web57/web`.
- **PHP** : 8.3 (Laravel 13 exige `php >= 8.3` — le site est créé par défaut en 8.2 dans ISPConfig, il faut explicitement sélectionner 8.3 dans les paramètres du site sinon le déploiement plante avec une erreur `platform_check.php`).
- **Base de données** : MySQL, base `c7meexeo`, utilisateur dédié `c7meexeo` (créés comme deux objets ISPConfig distincts — `sites_database_add` puis `sites_database_user_add` — puis liés via `sites_database_update` avec `database_user_id`).
- **SSL** : Let's Encrypt, activé une fois le DNS propagé (nécessite la validation HTTP-01, donc ne fonctionne qu'après que le domaine pointe vers le serveur).

## Étapes de déploiement

1. **Provisionner le site sur ISPConfig** (API JSON-RPC `https://180.149.197.201:8080/remote/json.php`) :
   - `sites_web_domain_add` → crée le vhost Apache.
   - Récupérer le vrai `document_root` assigné, puis `sites_web_domain_update` avec `apache_directives` pointant sur `.../web/public` (racine réelle Laravel).
   - `sites_web_domain_update` avec `server_php_id` = l'ID correspondant à PHP 8.3 (voir table `server_php`).
   - `sites_web_domain_update` avec `ssl_letsencrypt: "y"` une fois le DNS propagé.

2. **Provisionner la base de données** :
   - `sites_database_add` (sans mettre `database_user`/`database_password` en champs plats — ignorés silencieusement).
   - `sites_database_user_add` pour créer l'utilisateur MySQL.
   - `sites_database_update` avec `database_user_id` pour lier les deux.

3. **Transférer le code** :
   - Archive locale (`tar czf`, en excluant `node_modules`, `vendor`, `.git`, `.env`, `storage/logs/*`).
   - `scp` vers le serveur, extraction dans `/var/www/clients/client7/web57/web/`.
   - `chown -R web57:client7` sur tout le répertoire.

4. **Configurer l'environnement production** :
   - Écrire un `.env` de production (voir `.env.example` pour la liste des clés) avec `APP_ENV=production`, `APP_URL=https://admin.masterclays.net`, les identifiants `DB_*` de la base créée à l'étape 2.
   - `chmod 640` + `chown web57:client7` sur le `.env`.
   - `composer install --no-dev --optimize-autoloader` (en tant que `web57`, avec le binaire `php8.3` explicite — composer est à `/usr/local/bin/composer`).
   - `php artisan key:generate --force`.

5. **Base de données applicative** :
   - `php artisan migrate --force`.
   - Créer le compte admin : `php artisan db:seed --class=AdminUserSeeder --force`.
     - ⚠️ **Piège** : ce seeder lit `ADMIN_EMAIL` / `ADMIN_NAME` / `ADMIN_PASSWORD` via `env()`. Si le cache de config (`config:cache`) a déjà été généré, `env()` ne relit plus le `.env` et retombe sur un mot de passe aléatoire généré à la volée (jamais affiché). **Toujours lancer `php artisan config:clear` avant le seed**, puis re-cacher après si besoin.
     - Le seeder est idempotent (`firstOrCreate` sur l'email) : si le compte existe déjà avec le mauvais mot de passe, il faut le corriger directement (`php artisan tinker`, réassigner `$user->password` puis `save()` — le cast `'password' => 'hashed'` sur le modèle `User` se charge du hash).

6. **Build des assets front** :
   - `npm ci && npm run build` — **doit être exécuté directement sur le serveur**, pas en local : `vite.config.js` utilise le plugin `laravel-vite-plugin/fonts` (`bunny`) qui télécharge les polices depuis le réseau au moment du build. Si l'environnement local n'a pas d'accès réseau sortant, le build échoue avec `ETIMEDOUT`, alors que c'est identique sur le serveur.
   - Node/npm n'est disponible que dans le PATH de `root` (nvm) sur ce serveur, pas pour l'utilisateur `web57` → build en `root`, puis `chown -R web57:client7 .` à nouveau, puis suppression de `node_modules` (inutile en prod).

7. **Cache Laravel** :
   - `php artisan config:cache && php artisan route:cache && php artisan view:cache` (en tant que `web57`, **après** avoir fini toute opération qui dépend de `env()` — voir piège ci-dessus).

## Points de vigilance restants

- **Email** : `MAIL_MAILER=log` en production — aucun SMTP réel configuré. Les emails ne partent pas réellement (même limitation que Résidence Touvalem). À résoudre quand le client fournira des identifiants SMTP.
- **DNS** : géré par le client lui-même chez LWS, pas par nous. En cas de changement de serveur, il faudra le prévenir de mettre à jour l'enregistrement `A`.
