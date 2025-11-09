<?php

namespace App\Filament\Resources\EvenementsSanitaires;

use App\Filament\Resources\EvenementsSanitaires\Pages\CreateEvenementsSanitaire;
use App\Filament\Resources\EvenementsSanitaires\Pages\EditEvenementsSanitaire;
use App\Filament\Resources\EvenementsSanitaires\Pages\ListEvenementsSanitaires;
use App\Filament\Resources\EvenementsSanitaires\Pages\ViewEvenementsSanitaire;
use App\Filament\Resources\EvenementsSanitaires\Schemas\EvenementsSanitaireForm;
use App\Filament\Resources\EvenementsSanitaires\Schemas\EvenementsSanitaireInfolist;
use App\Filament\Resources\EvenementsSanitaires\Tables\EvenementsSanitairesTable;
use App\Models\EvenementsSanitaire;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EvenementsSanitaireResource extends Resource
{
    protected static ?string $model = EvenementsSanitaire::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return EvenementsSanitaireForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EvenementsSanitaireInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EvenementsSanitairesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvenementsSanitaires::route('/'),
            'create' => CreateEvenementsSanitaire::route('/create'),
            'view' => ViewEvenementsSanitaire::route('/{record}'),
            'edit' => EditEvenementsSanitaire::route('/{record}/edit'),
        ];
    }
}
