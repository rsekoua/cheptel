<?php

namespace App\Filament\Resources\ProduitSanitaires\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProduitSanitaireForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nom')
                    ->required()
                    ->maxLength(150)
                    ->helperText('Nom commercial du produit (ex: Porcilis® Ery+Parvo+Lepto)'),

                Select::make('type')
                    ->required()
                    ->options([
                        'vaccin' => 'Vaccin',
                        'antibiotique' => 'Antibiotique',
                        'antiparasitaire' => 'Antiparasitaire',
                        'autre' => 'Autre',
                    ])
                    ->native(false)
                    ->helperText('Catégorie du produit vétérinaire'),

                TextInput::make('laboratoire')
                    ->maxLength(100)
                    ->helperText('Nom du fabricant/laboratoire (ex: MSD Animal Health, Boehringer)'),

                TextInput::make('principe_actif')
                    ->maxLength(200)
                    ->helperText('Molécule active ou principe actif (ex: Ivermectine)'),

                TextInput::make('numero_amm')
                    ->maxLength(50)
                    ->helperText('Numéro d\'Autorisation de Mise sur le Marché (traçabilité réglementaire)'),

                TextInput::make('delai_attente_jours')
                    ->numeric()
                    ->suffix('jours')
                    ->helperText('Délai légal avant abattage après utilisation du produit'),

                Select::make('voie_administration')
                    ->options([
                        'injectable' => 'Injectable',
                        'orale' => 'Orale',
                        'topique' => 'Topique',
                    ])
                    ->native(false)
                    ->helperText('Mode d\'administration du produit'),

                TextInput::make('dosage_ml_kg')
                    ->numeric()
                    ->suffix('ml/kg')
                    ->step(0.001)
                    ->helperText('Dosage en millilitres par kilogramme de poids vif'),

                TextInput::make('stock_actuel')
                    ->numeric()
                    ->default(0)
                    ->helperText('Quantité actuellement en stock (nombre de doses ou flacons)'),

                TextInput::make('stock_alerte')
                    ->numeric()
                    ->helperText('Seuil d\'alerte pour réapprovisionnement')
            ]);
    }
}
