<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplateResource\Pages;
use App\Filament\Resources\TemplateResource\RelationManagers;
use App\Models\Template;
use App\Models\User;
use App\Models\Organogram;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
//use Filament\Resources\Form;
use Filament\Resources\Resource;
//use Filament\Resources\Table;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
//use Filament\Infolists\Components\Section;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Z3d0X\FilamentFabricator\Forms\Components\PageBuilder;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Illuminate\Support\Facades\Auth;

class TemplateResource extends Resource
{
    protected static ?string $model = Template::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()->schema([
                Group::make()
                ->schema([
                    TextInput::make('name')->label('Template Name'),
                    Select::make('layout')
                    ->label(__('filament-fabricator::page-resource.labels.layout'))
                    ->options(FilamentFabricator::getLayouts())
                    ->default('default')
                    ->required(),
                    Group::make()->schema(FilamentFabricator::getSchemaSlot('blocks.before')),

                    PageBuilder::make('blocks')
                        ->label(__('filament-fabricator::page-resource.labels.blocks'))->collapsible(),

                    Group::make()->schema(FilamentFabricator::getSchemaSlot('blocks.after')),
                    Repeater::make('viewable_by')
                    ->schema([
                        Select::make('users')
                        ->label('Select users')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->options(function(){
                            return User::where('role_id','>',1)->get()->pluck('name','id');
                        }),
                    ])
                    ->addable(false)
                    ->deletable(false)
                    ->reorderableWithDragAndDrop(false),
                    Toggle::make('active')
                ])
                ->columnSpan(2),
            ])
        ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Template Name'),
                TextColumn::make('layout')->label('Template Layout'),
                TextColumn::make('url')
                ->label(__('filament-fabricator::page-resource.labels.url'))
                ->toggleable()
                ->getStateUsing(function(){return "Preview Template";})
                ->url(function (Template $record){
                    return route('preview-template',$record);
                })
                ->visible()
                ->openUrlInNewTab(),
                TextColumn::make('created_by')
                    ->label('Created By')
                    ->getStateUsing(function(Template $record){
                    if($record->created_by !=null)
                    {
                        return $record->who_created->name;
                    }
                    else
                    {
                        return "NULL";
                    }
               }),
               IconColumn::make('active')->icons([
                'heroicon-o-x-circle' => fn($state, $record): bool => $record->active ==false,
                'heroicon-o-check-circle' => fn($state, $record): bool => $record->active ==true,
                ])
                ->colors([
                    'danger'=> fn($state, $record): bool => $record->active ==false,
                    'success' => fn($state, $record): bool => $record->active ==true
                ]),
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
            'index' => Pages\ListTemplates::route('/'),
            'create' => Pages\CreateTemplate::route('/create'),
            'edit' => Pages\EditTemplate::route('/{record}/edit'),
        ];
    }
}
