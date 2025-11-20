<?php

namespace App\Filament\Resources\Lots\Schemas;

use App\Models\Portee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LotForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informations générales')
                    ->schema([
                        TextInput::make('numero_lot')
                            ->label('Numéro du lot')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(100),

                        Select::make('type_lot')
                            ->label('Type de lot')
                            ->options([
                                'post_sevrage' => 'Post-sevrage',
                                'engraissement' => 'Engraissement',
                            ])
                            ->required()
                            ->native(false),

                        DatePicker::make('date_creation')
                            ->label('Date de création')
                            ->required()
                            ->default(now())
                            ->native(false),

                        Select::make('statut_lot')
                            ->label('Statut')
                            ->options([
                                'actif' => 'Actif',
                                'transfere' => 'Transféré',
                                'vendu' => 'Vendu',
                                'cloture' => 'Clôturé',
                            ])
                            ->default('actif')
                            ->required()
                            ->native(false),
                    ])
                    ->columns(2),

                Section::make('Portées sevrées')
                    ->description('Sélectionnez les portées sevrées à inclure dans ce lot. Seules les portées du même mois/année de naissance peuvent être regroupées.')
                    ->schema([
                        Select::make('portees')
                            ->label('Portées disponibles')
                            ->multiple()
                            ->relationship(
                                name: 'portees',
                                titleAttribute: 'id',
                                modifyQueryUsing: fn ($query, $get, $record) => static::filterPortees($query, $get, $record)
                            )
                            ->getOptionLabelFromRecordUsing(fn (Portee $record) => $record->identification_format)
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if (! empty($state)) {
                                    static::updateNbAnimaux($state, $set);
                                }
                            })
                            ->saveRelationshipsUsing(fn () => null)
                            ->dehydrated(false)
                            ->required()
                            ->columnSpanFull()
                            ->helperText('Maintenez Ctrl (ou Cmd) pour sélectionner plusieurs portées'),
                    ]),

                Section::make('Données du lot')
                    ->description('Ces données sont calculées automatiquement à partir des portées sélectionnées')
                    ->schema([
                        TextInput::make('nb_animaux_depart')
                            ->label('Nombre d\'animaux au départ')
                            ->required()
                            ->numeric()
                            ->readOnly()
                            ->default(0),

                        TextInput::make('nb_animaux_actuel')
                            ->label('Nombre d\'animaux actuel')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),

                Section::make('Sortie du lot')
                    ->description('Informations concernant la sortie du lot')
                    ->schema([
                        DatePicker::make('date_sortie')
                            ->label('Date de sortie')
                            ->native(false),

                        TextInput::make('nb_animaux_sortie')
                            ->label('Nombre d\'animaux sortis')
                            ->numeric()
                            ->minValue(0),

                        TextInput::make('destination_sortie')
                            ->label('Destination')
                            ->maxLength(200),
                    ])
                    ->columns(3)
                    ->collapsed(),

                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
            ]);
    }

    protected static function filterPortees($query, $get, $record)
    {
        $selectedPortees = $get('portees') ?? [];

        if (! empty($selectedPortees)) {
            $firstPortee = Portee::find(is_array($selectedPortees) ? $selectedPortees[0] : $selectedPortees);
            if ($firstPortee && $firstPortee->mois_annee_naissance) {
                $moisAnnee = $firstPortee->mois_annee_naissance;
                $query->availableForLot($moisAnnee);
            } else {
                $query->availableForLot();
            }
        } else {
            $query->availableForLot();
        }

        if ($record) {
            $query->orWhereHas('lots', function ($q) use ($record) {
                $q->where('lots.id', $record->id);
            });
        }

        return $query->with('animal');
    }

    protected static function updateNbAnimaux($state, $set)
    {
        $porteeIds = is_array($state) ? $state : [$state];
        $totalSevres = Portee::whereIn('id', $porteeIds)->sum('nb_sevres');

        $set('nb_animaux_depart', $totalSevres);
        $set('nb_animaux_actuel', $totalSevres);
    }
}
