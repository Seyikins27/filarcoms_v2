<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Filament\Resources\BlogResource\RelationManagers;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AmidEsfahani\FilamentTinyEditor\TinyEditor;
use Illuminate\Support\Str;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    
    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(6)
            ->schema([
                Group::make()
                    ->columnSpan(4)
                    ->schema([
                        Section::make()->schema([
                            TextInput::make('title')
                                ->required()
                                ->label('Title')
                                ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                                ->live( debounce: 250)
                                ->debounce(250),
                            FileUpload::make('image')
                                ->image()
                                ->directory('blogs'),
                            RichEditor::make('content')
                                ->fileAttachmentsDisk('public')
                                ->fileAttachmentsVisibility('public')
                                ->fileAttachmentsDirectory('contents')
                                ->required(),
                            Toggle::make('archived')->label('Archive'),
                            Toggle::make('active')
                        
                        ])
                        ]),
                Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Section::make()->schema([
                            TextInput::make('slug')->required(),
                            TextInput::make('author')->required(),
                            TagsInput::make('seo_tags')->label('SEO Tags')->required(),
                            TagsInput::make('tags')->label('Topical Tags')->required(),
                            Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->required(),
                        ])
                        
                    ])
               
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable()
                    ->getStateUsing(fn (?Blog $record) => $record->slug?: null)
                    ->url(fn (?Blog $record) => route('blog-'.$record->slug) ?: null, true)
                    ->visible(config('filament-fabricator.routing.enabled'))
                    ->icon('heroicon-o-arrow-top-right-on-square'),
                Tables\Columns\TextColumn::make('author')->searchable(),
                Tables\Columns\TextColumn::make('seo_tags')->searchable()->badge()->separator(','),
                Tables\Columns\TextColumn::make('tags')->searchable()->badge()->separator(',')->color('success'),
                Tables\Columns\IconColumn::make('archived')->sortable()->boolean(),
                Tables\Columns\IconColumn::make('active')->sortable()->boolean(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}
