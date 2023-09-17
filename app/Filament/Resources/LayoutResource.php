<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LayoutResource\Pages;
use App\Filament\Resources\LayoutResource\RelationManagers;
use App\Models\Layout;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class LayoutResource extends Resource
{
    protected static ?string $model = Layout::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Developer Settings';

    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')->unique(function($context){
                        if($context==="edit")
                        {
                            return false;
                        }
                    })->required()->disabledOn('edit'),
                    Toggle::make('is_buildable')->label('Buildable Layout')->helperText('If this layout will need to be built/compiled e.g tailwind')
                    ->reactive(),
                    Textarea::make('build_command')->label('command')->required()->hidden(function(Callable $get){
                        if($get('is_buildable')==true)
                        {
                            return false;
                        }
                        return true;
                    }),

                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Layout Name')->searchable(),
                IconColumn::make('is_buildable')->icons([
                    'heroicon-o-x-circle' => fn($state, $record): bool => $record->is_buildable ==false,
                    'heroicon-o-check-circle' => fn($state, $record): bool => $record->is_buildable ==true,
                ])
                ->colors([
                    'danger'=> fn($state, $record): bool => $record->is_buildable ==false,
                    'success' => fn($state, $record): bool => $record->is_buildable ==true
                ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('View Layout')->url(function(Layout $record){
                    return LayoutResource::getUrl('view',['record'=>$record]);
              })->openUrlInNewTab(),
                Tables\Actions\Action::make('Build Layout')->action(function(Layout $record){
                    //$bl=BuildLayout::dispatch($record->build_command);
                    //dd($bl);
                })->visible(function(Layout $record){
                    if($record->is_buildable==true)
                    {
                        return true;
                    }
                })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListLayouts::route('/'),
            'create' => Pages\CreateLayout::route('/create'),
            'edit' => Pages\EditLayout::route('/{record}/edit'),
            'view' => Pages\ViewLayout::route('/{record}/view')
        ];
    }
}
