<?php

namespace App\Filament\Resources\CycleReproductions\Tables;

use App\Filament\Resources\CycleReproductions\Actions\MiseBasAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class CycleReproductionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('details')
                    ->label('Cycle de reproduction')
                    ->html()
                    ->state(function ($record): HtmlString {
                        $truie = $record->animal;
                        $premiereSaillie = $record->saillies()->orderBy('date_heure')->first();
                        $verrat = $premiereSaillie?->verrat;

                        $statutLabel = match ($record->statut_cycle) {
                            'gestation_en_cours' => 'Gestation',
                            'mise_bas_effectuee' => 'Mise-bas',
                            'termine_echec' => 'Échec',
                            'termine_succes' => 'Terminer',
                            'en_lactation' => 'Lactation',
                            default => $record->statut_cycle,
                        };

                        $statutColor = match ($record->statut_cycle) {
                            'gestation_en_cours' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                            'mise_bas_effectuee' => ['bg' => '#d1fae5', 'text' => '#065f46'],
                            'termine_succes' => ['bg' => '#d1fae5', 'text' => '#065f46'],
                            'termine_echec' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                            'en_lactation' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                            default => ['bg' => '#f3f4f6', 'text' => '#374151'],
                        };

                        return new HtmlString(Blade::render('
                            <div style="padding: 12px 0; display: flex; justify-content: space-between; gap: 12px;">
                                {{-- Colonne gauche --}}
                                <div style="flex: 1;">
                                    {{-- Ligne 1: Truie × Verrat --}}
                                    <div style="display: flex; align-items: baseline; gap: 6px;">
                                        <span style="font-size: 1.125rem; font-weight: 700; color: #ec4899;">{{ $truieNumero }}</span>
                                        @if($verratNumero)
                                        <span style="font-size: 0.875rem; color: #9ca3af;">×</span>
                                        <span style="font-size: 1.125rem; font-weight: 700; color: #3b82f6;">{{ $verratNumero }}</span>
                                        @endif
                                    </div>

                                    {{-- Ligne 2: Dates --}}
                                    <div style="display: flex; gap: 24px; margin-top: 10px;">
                                        @if($dateSaillie)
                                        <div>
                                            <span style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af;">1ère saillie</span>
                                            <p style="font-size: 0.875rem; font-weight: 500; color: #374151; margin: 2px 0 0 0;">{{ $dateSaillie }}</p>
                                        </div>
                                        @endif
                                        @if($dateMiseBas)
                                        <div>
                                            <span style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af;">Mise bas prévue</span>
                                            <p style="font-size: 0.875rem; font-weight: 600; color: #f59e0b; margin: 2px 0 0 0;">{{ $dateMiseBas }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Colonne droite: Statut + Portée --}}
                                <div style="text-align: right;">
                                    <span style="padding: 4px 10px; font-size: 0.7rem; font-weight: 600; border-radius: 6px; background: {{ $statutBg }}; color: {{ $statutText }};">
                                        {{ $statutLabel }}
                                    </span>
                                    @if($nbPortees)
                                    <div style="margin-top: 8px;">
                                        <span style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af;">Portée</span>
                                        <p style="font-size: 0.875rem; font-weight: 600; color: #10b981; margin: 2px 0 0 0;">{{ $nbPortees }} porcelets</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        ', [
                            'truieNumero' => $truie?->numero_identification ?? '-',
                            'verratNumero' => $verrat?->numero_identification,
                            'statutLabel' => $statutLabel,
                            'statutBg' => $statutColor['bg'],
                            'statutText' => $statutColor['text'],
                            'dateSaillie' => $premiereSaillie?->date_heure?->format('d/m/Y'),
                            'dateMiseBas' => $record->statut_cycle === 'termine_echec' ? null : $record->date_mise_bas_prevue_formatee,
                            'nbPortees' => $record->statut_cycle === 'termine_succes' || 'en_lactation' ? $record->portee?->nb_total : null,
                        ]));
                    })
                    ->searchable(['animal.numero_identification']),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                MiseBasAction::make(),
                ViewAction::make()->iconButton(),
                EditAction::make()->iconButton(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date_debut', 'desc');
    }
}
