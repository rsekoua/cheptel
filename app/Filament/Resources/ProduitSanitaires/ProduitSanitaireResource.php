<?php

namespace App\Filament\Resources\ProduitSanitaires;

use App\Filament\Resources\ProduitSanitaires\Pages\CreateProduitSanitaire;
use App\Filament\Resources\ProduitSanitaires\Pages\EditProduitSanitaire;
use App\Filament\Resources\ProduitSanitaires\Pages\ListProduitSanitaires;
use App\Filament\Resources\ProduitSanitaires\Pages\ViewProduitSanitaire;
use App\Filament\Resources\ProduitSanitaires\Schemas\ProduitSanitaireForm;
use App\Filament\Resources\ProduitSanitaires\Schemas\ProduitSanitaireInfolist;
use App\Filament\Resources\ProduitSanitaires\Tables\ProduitSanitairesTable;
use App\Models\ProduitSanitaire;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProduitSanitaireResource extends Resource
{
    protected static ?string $model = ProduitSanitaire::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|null|\UnitEnum $navigationGroup = "Parametres";

    public static function form(Schema $schema): Schema
    {
        return ProduitSanitaireForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProduitSanitaireInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProduitSanitairesTable::configure($table);
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
            'index' => ListProduitSanitaires::route('/'),
//            'create' => CreateProduitSanitaire::route('/create'),
//            'view' => ViewProduitSanitaire::route('/{record}'),
//            'edit' => EditProduitSanitaire::route('/{record}/edit'),
        ];
    }
}
