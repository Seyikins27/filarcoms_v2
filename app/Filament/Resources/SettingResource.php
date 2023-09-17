<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Filament\Resources\SettingResource\RelationManagers;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Closure;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    //protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationGroup = 'Site Configuration';

    protected static ?string $navigationLabel = 'Configuration';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\TextInput::make('key')->label('Key')->required(),
                    Forms\Components\TextInput::make('value')->label('Value'),
                    Forms\Components\Select::make('type')
                    ->label('Setting Type')
                    ->options(static::classes_in_namespace())
                    ->searchable()
                    ->reactive()
                    ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key'),
                TextColumn::make('value'),
                TextColumn::make('type'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    protected static function classes_in_namespace() {
        $dir=base_path().'/vendor/filament/forms/src/Components';
        $classes = scandir($dir);
        $theClasses=[];
        foreach ($classes as $key => $link) {
            if(is_dir($dir."/".$link)){
                unset($classes[$key]);
            }
            else{
                $filename = pathinfo($link, PATHINFO_FILENAME);
                $theClasses[$filename]=$filename;
            }

        }
        $theClasses["CuratorPicker"]="CuratorPicker";
        //dd($theClasses);
        return $theClasses;
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
