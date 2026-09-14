<?php

return [
    'nav_groups' => [
        'OPÉRATIONS' => ['residence', 'hotel', 'locative', 'evenementiel', 'vehicules'],
        'SUPPORT' => ['stock', 'rh', 'clients', 'fournisseurs'],
        'COMMERCE & FINANCE' => ['ecommerce', 'finance', 'reports'],
        'ADMINISTRATION' => ['permissions', 'notifications', 'security', 'settings'],
    ],

    'modules' => [
        'residence' => [
            'label' => 'Résidence', 'sub' => 'Gestion des résidences', 'icon' => 'RE',
            'color' => '#2563EB', 'route' => null, 'addLabel' => 'Réservation', 'generic' => true,
            'kpis' => [
                ['label' => 'Résidences actives', 'value' => '12'],
                ['label' => 'Chambres/apparts occupés', 'value' => '45 / 58'],
                ['label' => 'Réservations ce mois', 'value' => '128'],
                ['label' => "Taux d'occupation", 'value' => '78%'],
            ],
            'columns' => ['Client', 'Logement', 'Arrivée', 'Départ', 'Montant'],
            'rows' => [
                ['cells' => ['Kouassi Jean', 'Villa Deluxe - Cocody', '21/05/2025', '25/05/2025', '450 000 FCFA'], 'status' => 'Confirmée'],
                ['cells' => ['Yao Michel', 'Appartement F2 - Les Palmiers', '20/05/2025', '22/05/2025', '180 000 FCFA'], 'status' => 'En attente'],
                ['cells' => ["N'Guessan A.", 'Studio Riviera', '19/05/2025', '30/05/2025', '620 000 FCFA'], 'status' => 'Confirmée'],
                ['cells' => ['Diarra Fatou', 'Duplex Marcory', '18/05/2025', '19/05/2025', '340 000 FCFA'], 'status' => 'Terminée'],
                ['cells' => ['Ouattara Salif', 'Villa Prestige - Bingerville', '24/05/2025', '29/05/2025', '780 000 FCFA'], 'status' => 'Confirmée'],
            ],
        ],
        'hotel' => [
            'label' => 'Hôtel', 'sub' => 'Chambres & réservations', 'icon' => 'HT',
            'color' => '#16A34A', 'route' => null, 'addLabel' => 'Réservation', 'generic' => true,
            'kpis' => [
                ['label' => 'Chambres occupées', 'value' => '85 / 120'],
                ['label' => "Taux d'occupation", 'value' => '70,8%'],
                ['label' => 'Réservations ce mois', 'value' => '243'],
                ['label' => 'Séjours en cours', 'value' => '32'],
            ],
            'columns' => ['Client', 'Chambre', 'Type', 'Arrivée', 'Départ', 'Montant'],
            'rows' => [
                ['cells' => ["N'Guessan A.", '204', 'Standard', '21/05/2025', '23/05/2025', '90 000 FCFA'], 'status' => 'Confirmée'],
                ['cells' => ['Diarra Fatou', 'Suite 12', 'Junior Suite', '20/05/2025', '22/05/2025', '150 000 FCFA'], 'status' => 'Confirmée'],
                ['cells' => ['Kouassi Jean', '305', 'Deluxe', '19/05/2025', '21/05/2025', '120 000 FCFA'], 'status' => 'En attente'],
                ['cells' => ['Traoré Awa', 'Suite 03', 'Suite Prestige', '18/05/2025', '20/05/2025', '220 000 FCFA'], 'status' => 'Confirmée'],
                ['cells' => ['Bakayoko S.', '118', 'Standard', '17/05/2025', '18/05/2025', '60 000 FCFA'], 'status' => 'Terminée'],
            ],
        ],
        'locative' => [
            'label' => 'Gestion locative', 'sub' => 'Biens & contrats', 'icon' => 'GL',
            'color' => '#EA580C', 'route' => 'properties.index', 'addLabel' => 'Contrat', 'generic' => false,
            'kpis' => [], 'columns' => [], 'rows' => [],
        ],
        'evenementiel' => [
            'label' => 'Événementiel', 'sub' => 'Événements & prestations', 'icon' => 'EV',
            'color' => '#7C3AED', 'route' => null, 'addLabel' => 'Événement', 'generic' => true,
            'kpis' => [
                ['label' => 'Événements ce mois', 'value' => '7'],
                ['label' => 'Équipements en stock', 'value' => '312'],
                ['label' => 'Disponibilité équipements', 'value' => '65%'],
            ],
            'columns' => ['Événement', 'Date', 'Lieu', 'Responsable', 'Budget'],
            'rows' => [
                ['cells' => ['Mariage Koffi & Aya', '14/06/2025', 'Salle Prestige - Abidjan', 'Awa Traoré', '3 500 000 FCFA'], 'status' => 'Confirmé'],
                ['cells' => ['Séminaire MASTERCLAYS', '02/06/2025', 'Hôtel Résidence', 'Jean Kouakou', '1 200 000 FCFA'], 'status' => 'En préparation'],
                ['cells' => ['Anniversaire Konan', '21/05/2025', 'Villa Deluxe', 'Fatou Diarra', '800 000 FCFA'], 'status' => 'Confirmé'],
                ['cells' => ['Baptême famille Yao', '29/05/2025', 'Résidence Les Palmiers', 'Michel Yao', '450 000 FCFA'], 'status' => 'En attente'],
                ['cells' => ["Gala d'entreprise", '18/06/2025', 'Salle Prestige', 'Awa Traoré', '5 000 000 FCFA'], 'status' => 'Confirmé'],
            ],
        ],
        'vehicules' => [
            'label' => 'Véhicules', 'sub' => 'Parc & locations', 'icon' => 'VH',
            'color' => '#0284C7', 'route' => null, 'addLabel' => 'Véhicule', 'generic' => true,
            'kpis' => [
                ['label' => 'Véhicules', 'value' => '45'],
                ['label' => 'Disponibles', 'value' => '26'],
                ['label' => 'Loués', 'value' => '15'],
                ['label' => 'En maintenance', 'value' => '4'],
            ],
            'columns' => ['Véhicule', 'Immatriculation', 'Chauffeur', 'Dernière révision'],
            'rows' => [
                ['cells' => ['Toyota Hilux', 'CI-2201-AB', 'Bakayoko S.', '12/04/2025'], 'status' => 'Disponible'],
                ['cells' => ['Toyota Corolla', 'CI-1187-CD', 'Ouattara Salif', '28/03/2025'], 'status' => 'Loué'],
                ['cells' => ['Hyundai Tucson', 'CI-0765-EF', '—', '15/02/2025'], 'status' => 'En maintenance'],
                ['cells' => ['Mercedes Sprinter', 'CI-3320-GH', 'Diarra Fatou', '30/04/2025'], 'status' => 'Disponible'],
                ['cells' => ['Kia Picanto', 'CI-4402-IJ', 'Kouassi Jean', '10/04/2025'], 'status' => 'Loué'],
            ],
        ],
        'stock' => [
            'label' => 'Gestion de stock', 'sub' => 'Produits & inventaire', 'icon' => 'ST',
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
            'label' => 'Ressources humaines', 'sub' => 'Employés & paie', 'icon' => 'RH',
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
            'label' => 'Clients', 'sub' => 'Clients & prospects', 'icon' => 'CL',
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
            'label' => 'Fournisseurs', 'sub' => 'Fournisseurs & commandes', 'icon' => 'FO',
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
            'label' => 'E-commerce', 'sub' => 'Boutique en ligne', 'icon' => 'EC',
            'color' => '#E11D48', 'route' => null, 'addLabel' => 'Commande', 'generic' => true,
            'kpis' => [
                ['label' => 'Commandes ce mois', 'value' => '96'],
                ['label' => 'Revenus e-commerce', 'value' => '1 850 000 FCFA'],
                ['label' => 'Produits en ligne', 'value' => '214'],
            ],
            'columns' => ['Commande', 'Client', 'Produits', 'Montant'],
            'rows' => [
                ['cells' => ['#CMD-1042', 'Kouassi Jean', '2 articles', '85 000 FCFA'], 'status' => 'Livrée'],
                ['cells' => ['#CMD-1041', 'Diarra Fatou', '1 article', '32 000 FCFA'], 'status' => 'Expédiée'],
                ['cells' => ['#CMD-1040', "N'Guessan A.", '3 articles', '145 000 FCFA'], 'status' => 'En préparation'],
                ['cells' => ['#CMD-1039', 'Yao Michel', '1 article', '19 000 FCFA'], 'status' => 'Nouvelle'],
                ['cells' => ['#CMD-1038', 'Ouattara Salif', '4 articles', '210 000 FCFA'], 'status' => 'Annulée'],
            ],
        ],
        'finance' => ['label' => 'Comptabilité', 'sub' => 'Finances & rapports', 'icon' => 'CO', 'color' => '#059669', 'route' => 'masterclays.finance', 'generic' => false],
        'reports' => ['label' => 'Rapports & Statistiques', 'sub' => 'Rapports & exports', 'icon' => 'RA', 'color' => '#4F46E5', 'route' => 'masterclays.reports', 'generic' => false],
        'permissions' => ['label' => 'Utilisateurs & Permissions', 'sub' => "Comptes & droits d'accès", 'icon' => 'UP', 'color' => '#9333EA', 'route' => 'masterclays.permissions', 'generic' => false],
        'notifications' => ['label' => 'Notifications', 'sub' => 'Alertes & rappels', 'icon' => 'NO', 'color' => '#DC2626', 'route' => 'masterclays.notifications', 'generic' => false],
        'security' => ['label' => 'Sécurité', 'sub' => 'Connexions & journaux', 'icon' => 'SE', 'color' => '#475569', 'route' => 'masterclays.security', 'generic' => false],
        'settings' => ['label' => 'Paramètres', 'sub' => 'Configuration du système', 'icon' => 'PA', 'color' => '#57534E', 'route' => 'masterclays.settings', 'generic' => false],
    ],
];
