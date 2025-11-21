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
                            <div style="display: flex; align-items: center; gap: 12px; padding: 4px 0;">
                                {{-- Avatar cochon SVG --}}
                                <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: {{ $type === "truie" ? "#fdf2f8" : "#eff6ff" }}; border: 2px solid {{ $type === "truie" ? "#fbcfe8" : "#bfdbfe" }};">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" style="width: 36px; height: 36px;">
                                        <ellipse cx="50" cy="55" rx="35" ry="28" fill="{{ $type === "truie" ? "#F4A3B3" : "#93c5fd" }}"/>
                                        <circle cx="50" cy="40" r="25" fill="{{ $type === "truie" ? "#F4A3B3" : "#93c5fd" }}"/>
                                        <ellipse cx="30" cy="22" rx="10" ry="12" fill="{{ $type === "truie" ? "#E88A9A" : "#60a5fa" }}" transform="rotate(-20 30 22)"/>
                                        <ellipse cx="70" cy="22" rx="10" ry="12" fill="{{ $type === "truie" ? "#E88A9A" : "#60a5fa" }}" transform="rotate(20 70 22)"/>
                                        <ellipse cx="30" cy="24" rx="5" ry="7" fill="{{ $type === "truie" ? "#D4707F" : "#3b82f6" }}" transform="rotate(-20 30 24)"/>
                                        <ellipse cx="70" cy="24" rx="5" ry="7" fill="{{ $type === "truie" ? "#D4707F" : "#3b82f6" }}" transform="rotate(20 70 24)"/>
                                        <ellipse cx="50" cy="48" rx="12" ry="9" fill="{{ $type === "truie" ? "#E88A9A" : "#60a5fa" }}"/>
                                        <ellipse cx="45" cy="48" rx="3" ry="4" fill="{{ $type === "truie" ? "#D4707F" : "#3b82f6" }}"/>
                                        <ellipse cx="55" cy="48" rx="3" ry="4" fill="{{ $type === "truie" ? "#D4707F" : "#3b82f6" }}"/>
                                        <circle cx="40" cy="35" r="5" fill="#2D2D2D"/>
                                        <circle cx="60" cy="35" r="5" fill="#2D2D2D"/>
                                        <circle cx="42" cy="33" r="2" fill="#FFFFFF"/>
                                        <circle cx="62" cy="33" r="2" fill="#FFFFFF"/>
                                    </svg>
                                </div>

                                {{-- Infos principales --}}
                                <div style="flex: 1; min-width: 0;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <span style="font-size: 1rem; font-weight: 600; color: #111827;">{{ $numero }}</span>
                                        <span style="display: inline-flex; align-items: center; padding: 2px 8px; font-size: 0.625rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; border-radius: 6px; background-color: {{ $isActif ? "#dcfce7" : "#fef2f2" }}; color: {{ $isActif ? "#166534" : "#991b1b" }};">
                                            {{ $isActif ? "Actif" : "Inactif" }}
                                        </span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px; margin-top: 4px; flex-wrap: wrap;">
                                        <span style="font-size: 0.75rem; font-weight: 500; color: {{ $type === "truie" ? "#ec4899" : "#3b82f6" }};">{{ ucfirst($type) }}</span>
                                        <span style="color: #d1d5db;">•</span>
                                        <span style="font-size: 0.75rem; color: #6b7280;">{{ $race }}</span>
                                        <span style="color: #d1d5db;">•</span>
                                        <span style="font-size: 0.75rem; color: #9ca3af;">{{ $naissance }}</span>
                                    </div>
                                </div>

                                {{-- Chevron --}}
                                <x-heroicon-o-chevron-right style="width: 20px; height: 20px; color: #d1d5db; flex-shrink: 0;" />
                            </div>
                        ', [
                            'numero' => $record->numero_identification,
                            'type' => $record->type_animal,
                            'race' => $record->race?->nom ?? '-',
                            'naissance' => $record->age_formate ?? '-',
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
