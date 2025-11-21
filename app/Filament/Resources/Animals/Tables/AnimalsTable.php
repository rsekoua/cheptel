<?php

namespace App\Filament\Resources\Animals\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class AnimalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Layout mobile-first avec grid HTML
                TextColumn::make('details')
                    ->label('Animal')
                    ->html()
                    ->state(function ($record): HtmlString {
                        return new HtmlString(Blade::render('
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <div style="font-size: 1.125rem; font-weight: 700; color: #111827;">{{ $numero }}</div>
                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <x-heroicon-o-user style="width: 16px; height: 16px; color: #9ca3af;" />
                                        <div>
                                            <span style="font-size: 0.75rem; color: #6b7280;">Type</span>
                                            <p style="font-size: 0.875rem; color: #111827; margin: 0;">{{ $type }}</p>
                                        </div>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <x-heroicon-o-tag style="width: 16px; height: 16px; color: #9ca3af;" />
                                        <div>
                                            <span style="font-size: 0.75rem; color: #6b7280;">Race</span>
                                            <p style="font-size: 0.875rem; color: #111827; margin: 0;">{{ $race }}</p>
                                        </div>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <x-heroicon-o-calendar style="width: 16px; height: 16px; color: #9ca3af;" />
                                        <div>
                                            <span style="font-size: 0.75rem; color: #6b7280;">Naissance</span>
                                            <p style="font-size: 0.875rem; color: #111827; margin: 0;">{{ $naissance }}</p>
                                        </div>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <x-heroicon-o-map-pin style="width: 16px; height: 16px; color: #9ca3af;" />
                                        <div>
                                            <span style="font-size: 0.75rem; color: #6b7280;">Origine</span>
                                            <p style="font-size: 0.875rem; color: #111827; margin: 0;">{{ $origine }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div style="padding-top: 4px;">
                                    <span style="display: inline-flex; align-items: center; padding: 4px 8px; font-size: 0.75rem; font-weight: 500; border-radius: 9999px; background-color: {{ $isActif ? "#dcfce7" : "#fee2e2" }}; color: {{ $isActif ? "#166534" : "#991b1b" }};">
                                        <x-heroicon-o-check-circle style="width: 14px; height: 14px; margin-right: 4px;" @if(!$isActif) class="hidden" @endif />
                                        <x-heroicon-o-x-circle style="width: 14px; height: 14px; margin-right: 4px;" @if($isActif) class="hidden" @endif />
                                        {{ $isActif ? "Actif" : "Inactif" }}
                                    </span>
                                </div>
                            </div>
                        ', [
                            'numero' => $record->numero_identification,
                            'type' => $record->type_animal,
                            'race' => $record->race?->nom ?? '-',
                            'naissance' => $record->date_naissance?->format('d/m/Y') ?? '-',
                            'origine' => $record->origine ?? '-',
                            'statut' => $record->statut_actuel ?? '-',
                            'isActif' => $record->is_actif ?? false,
                        ]));
                    })
                    ->searchable(['numero_identification', 'type_animal', 'origine']),

                // Colonnes visibles uniquement sur desktop
//                TextColumn::make('date_entree')
//                    ->label('Entrée')
//                    ->date()
//                  //  ->sortable()
//                    ->visibleFrom('md'),
//                TextColumn::make('provenance')
//                    ->searchable()
//                    ->visibleFrom('md'),
//                TextColumn::make('numero_mere')
//                 //   ->toggleable(isToggledHiddenByDefault: true)
//                    ->searchable()
//                    ->visibleFrom('md'),
//                TextColumn::make('numero_pere')
//                //    ->toggleable(isToggledHiddenByDefault: true)
//                    ->searchable()
//                    ->visibleFrom('md'),
//                TextColumn::make('date_reforme')
//                    ->date()
//                  //  ->sortable()
//                    ->visibleFrom('md'),
                //                TextColumn::make('created_at')
                //                    ->dateTime()
                //                   // ->sortable()
                //                //    ->toggleable(isToggledHiddenByDefault: true)
                //                    ->visibleFrom('lg'),
                //                TextColumn::make('updated_at')
                //                    ->dateTime()
                //                   // ->sortable()
                //               //     ->toggleable(isToggledHiddenByDefault: true)
                //                    ->visibleFrom('lg'),
                //                TextColumn::make('deleted_at')
                //                    ->dateTime()
                //                    //->sortable()
                //                  //  ->toggleable(isToggledHiddenByDefault: true)
                //                    ->visibleFrom('lg'),
            ])
            ->filters([
                //  TrashedFilter::make(),
            ])
            ->deferFilters(false)
            ->recordActions([
                //                ViewAction::make()
                //                    ->iconButton()
                //                    ->labeledFrom('md'),
                EditAction::make()
                    ->iconButton()
                    ->labeledFrom('md'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
