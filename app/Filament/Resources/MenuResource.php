<?php
namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\NavMenu;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = NavMenu::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    
    protected static ?int $navigationSort = 1;


    public static function getNavigationGroup(): ?string
    {
        return __('filament-menu-builder::menu-builder.navigation_group');
    }

    public static function getModelLabel(): string
    {
        return __('filament-menu-builder::menu-builder.menu');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-menu-builder::menu-builder.menus');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('filament-menu-builder::menu-builder.form_labels.name'))
                    ->required()
                    ->autofocus()
                    ->placeholder('Name')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament-menu-builder::menu-builder.form_labels.name'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Component')
                    ->copyable()
                    ->copyMessage(__('filament-menu-builder::menu-builder.component_copy_message'))
                    ->copyMessageDuration(3000)
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => "<x-filament-menu-builder::menu slug=\"{$state}\" />"),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make(__('filament-menu-builder::menu-builder.configure_menu'))
                    ->url(fn (NavMenu $record): string => static::getUrl('build', ['record' => $record]))
                    ->icon('heroicon-o-bars-3'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
            'build' => Pages\MenuBuilder::route('/{record}/build'),
        ];
    }
}
