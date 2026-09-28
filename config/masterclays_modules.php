<?php

return [
    'nav_groups' => [
        'OPÉRATIONS' => ['hotel', 'locative', 'evenementiel', 'vehicules'],
        'SUPPORT' => ['stock', 'rh', 'clients', 'fournisseurs'],
        'COMMERCE & FINANCE' => ['ecommerce', 'finance', 'reports'],
        'ADMINISTRATION' => ['permissions', 'notifications', 'security', 'settings'],
    ],

    'modules' => [
        'hotel' => [
            'label' => 'Hôtel', 'sub' => 'Chambres & séjours', 'icon' => 'bed-double',
            'color' => '#16A34A', 'route' => 'hotel-rooms.index', 'addLabel' => 'Séjour', 'generic' => false,
            'kpis' => [], 'columns' => [], 'rows' => [],
        ],
        'locative' => [
            'label' => 'Gestion locative', 'sub' => 'Biens & contrats', 'icon' => 'building',
            'color' => '#EA580C', 'route' => 'properties.index', 'addLabel' => 'Contrat', 'generic' => false,
            'kpis' => [], 'columns' => [], 'rows' => [],
        ],
        'evenementiel' => [
            'label' => 'Événementiel', 'sub' => 'Événements & équipements', 'icon' => 'calendar',
            'color' => '#7C3AED', 'route' => 'events.index', 'addLabel' => 'Événement', 'generic' => false,
            'kpis' => [], 'columns' => [], 'rows' => [],
        ],
        'vehicules' => [
            'label' => 'Véhicules', 'sub' => 'Parc & locations', 'icon' => 'car',
            'color' => '#0284C7', 'route' => 'vehicles.index', 'addLabel' => 'Véhicule', 'generic' => false,
            'kpis' => [], 'columns' => [], 'rows' => [],
        ],
        'stock' => [
            'label' => 'Gestion de stock', 'sub' => 'Produits & inventaire', 'icon' => 'package',
            'color' => '#0D9488', 'route' => null, 'addLabel' => 'Produit', 'generic' => true,
            'kpis' => [
                ['label' => 'Articles en stock', 'value' => '1 245'],
                ['label' => 'Équipements événementiel', 'value' => '312'],
                ['label' => 'Articles en rupture', 'value' => '12'],
            ],
            'columns' => ['Produit', 'Référence', 'Catégorie', 'Quantité', 'Seuil min'],
            'rows' => [
                ['cells' => ['Chaises pliantes', 'REF-2201', 'Événementiel', '340', '50'], 'status' => 'OK'],
                ['cells' => ['Nappes blanches', 'REF-1187', 'Événementiel', '12', '30'], 'status' => 'Stock faible'],
                ['cells' => ['Draps hôtel', 'REF-0765', 'Hôtel', '210', '40'], 'status' => 'OK'],
                ['cells' => ['Produits nettoyage', 'REF-3320', 'Résidence', '8', '25'], 'status' => 'Stock faible'],
                ['cells' => ['Pneus véhicules', 'REF-4402', 'Véhicules', '18', '10'], 'status' => 'OK'],
            ],
        ],
        'rh' => [
            'label' => 'Ressources humaines', 'sub' => 'Employés & paie', 'icon' => 'users',
            'color' => '#DB2777', 'route' => null, 'addLabel' => 'Employé', 'generic' => true,
            'kpis' => [
                ['label' => 'Employés actifs', 'value' => '18'],
                ['label' => 'Nouveaux ce mois', 'value' => '2'],
                ['label' => 'Congés en cours', 'value' => '3'],
            ],
            'columns' => ['Employé', 'Fonction', 'Service', 'Embauche'],
            'rows' => [
                ['cells' => ['Jean Kouakou', 'Réceptionniste', 'Hôtel', '03/2023'], 'status' => 'Actif'],
                ['cells' => ["N'Da Marie", 'Comptable', 'Comptabilité', '11/2022'], 'status' => 'Actif'],
                ['cells' => ['Silué Bakary', 'Chauffeur', 'Véhicules', '06/2024'], 'status' => 'Actif'],
                ['cells' => ['Aya Koffi', 'Agent de stock', 'Gestion de stock', '01/2024'], 'status' => 'Congé'],
                ['cells' => ['Traoré Awa', 'Chargée événementiel', 'Événementiel', '09/2021'], 'status' => 'Actif'],
            ],
        ],
        'clients' => [
            'label' => 'Clients', 'sub' => 'Clients & prospects', 'icon' => 'user-round',
            'color' => '#0891B2', 'route' => null, 'addLabel' => 'Client', 'generic' => true,
            'kpis' => [
                ['label' => 'Clients actifs', 'value' => '256'],
                ['label' => 'Nouveaux ce mois', 'value' => '14'],
                ['label' => 'Clients réguliers', 'value' => '182'],
            ],
            'columns' => ['Client', 'Contact', 'Activité principale', 'Total dépensé'],
            'rows' => [
                ['cells' => ['Kouassi Jean', '+225 07 01 02 03 04', 'Résidence', '2 340 000 FCFA'], 'status' => 'Régulier'],
                ['cells' => ["N'Guessan A.", '+225 05 06 07 08 09', 'Locatif', '980 000 FCFA'], 'status' => 'Régulier'],
                ['cells' => ['Yao Michel', '+225 01 02 03 04 05', 'Résidence', '450 000 FCFA'], 'status' => 'Nouveau'],
                ['cells' => ['Diarra Fatou', '+225 07 09 08 07 06', 'Hôtel', '1 120 000 FCFA'], 'status' => 'Régulier'],
                ['cells' => ['Ouattara Salif', '+225 05 04 03 02 01', 'Événementiel', '3 500 000 FCFA'], 'status' => 'Régulier'],
            ],
        ],
        'fournisseurs' => [
            'label' => 'Fournisseurs', 'sub' => 'Fournisseurs & commandes', 'icon' => 'truck',
            'color' => '#B45309', 'route' => null, 'addLabel' => 'Fournisseur', 'generic' => true,
            'kpis' => [
                ['label' => 'Fournisseurs actifs', 'value' => '32'],
                ['label' => 'Montant dû total', 'value' => '865 000 FCFA'],
            ],
            'columns' => ['Fournisseur', 'Produits/Services', 'Montant dû', 'Dernière commande'],
            'rows' => [
                ['cells' => ['Ivoire Déco', 'Mobilier événementiel', '450 000 FCFA', '10/05/2025'], 'status' => 'Payé'],
                ['cells' => ['CI Textiles', 'Linge hôtel & résidence', '280 000 FCFA', '02/05/2025'], 'status' => 'En attente'],
                ['cells' => ['AutoParts CI', 'Pièces véhicules', '120 000 FCFA', '28/04/2025'], 'status' => 'Payé'],
                ['cells' => ['Clean Pro', "Produits d'entretien", '60 000 FCFA', '05/05/2025'], 'status' => 'Impayé'],
                ['cells' => ['BuroPlus', 'Fournitures bureau', '35 000 FCFA', '01/05/2025'], 'status' => 'Payé'],
            ],
        ],
        'ecommerce' => [
            'label' => 'E-commerce', 'sub' => 'Boutique en ligne', 'icon' => 'shopping-cart',
            'color' => '#E11D48', 'route' => 'products.index', 'addLabel' => 'Produit', 'generic' => false,
            'kpis' => [], 'columns' => [], 'rows' => [],
        ],
        'finance' => ['label' => 'Comptabilité', 'sub' => 'Finances & rapports', 'icon' => 'wallet', 'color' => '#059669', 'route' => 'masterclays.finance', 'generic' => false],
        'reports' => ['label' => 'Rapports & Statistiques', 'sub' => 'Rapports & exports', 'icon' => 'bar-chart-3', 'color' => '#4F46E5', 'route' => 'masterclays.reports', 'generic' => false],
        'permissions' => ['label' => 'Utilisateurs & Permissions', 'sub' => "Comptes & droits d'accès", 'icon' => 'shield', 'color' => '#9333EA', 'route' => 'masterclays.permissions', 'generic' => false],
        'notifications' => ['label' => 'Notifications', 'sub' => 'Alertes & rappels', 'icon' => 'bell', 'color' => '#DC2626', 'route' => 'masterclays.notifications', 'generic' => false],
        'security' => ['label' => 'Sécurité', 'sub' => 'Connexions & journaux', 'icon' => 'lock', 'color' => '#475569', 'route' => 'masterclays.security', 'generic' => false],
        'settings' => ['label' => 'Paramètres', 'sub' => 'Configuration du système', 'icon' => 'settings', 'color' => '#57534E', 'route' => 'masterclays.settings', 'generic' => false],
    ],
];
