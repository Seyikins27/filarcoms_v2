<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Resources\PageResource\RelationManagers;
use App\Models\Page;
use App\Models\Organogram;
use App\Models\Template;
use App\Models\User;
use App\Services\OpenAIService;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
//use Filament\Resources\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Z3d0X\FilamentFabricator\Facades\FilamentFabricator;
use Z3d0X\FilamentFabricator\Forms\Components\PageBuilder;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Z3d0X\FilamentFabricator\Models\Contracts\Page as PageContract;
use Closure;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;


    /*public static function getModel(): string
    {
        return FilamentFabricator::getPageModel();
    }*/

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Group::make()
                    ->schema([
                        Select::make('templates')
                        ->label('Load From Existing Templates')
                        ->options(function (){
                           return Template::get_template()->pluck('name','id');
                        })
                        ->reactive()
                        ->preload()
                        ->searchable()
                        ->afterStateUpdated(function ($state, $livewire) {
                            if(blank($state)) {
                                $blocks = null;
                            } else {
                                $template = Template::find($state);
                                $blocks = $template->blocks;
                            }

                            $newState = [
                                ...$livewire->form->getRawState(),
                                'blocks' => $blocks,
                            ];

                            $livewire->form->fill($newState);
                        }),
                        Group::make()->schema(FilamentFabricator::getSchemaSlot('blocks.before')),

                        PageBuilder::make('blocks')
                            ->label(__('filament-fabricator::page-resource.labels.blocks'))->collapsible(),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('generateWithAI')
                                ->label(__('Generate with AI'))
                                ->action(function (Set $set, Get $get, array $data, OpenAIService $openAIService) {
                                    $blocks = $get('blocks');
                                    if ($data['generation_type'] === 'text') {
                                        $generatedHtml = $openAIService->generateHtml($data['prompt']);
                                        if (empty($generatedHtml)) {
                                            Notification::make()
                                                ->title(__('Error generating content'))
                                                ->body(__('The AI service failed to generate content. Please try again.'))
                                                ->danger()
                                                ->send();
                                            return;
                                        }
                                        $blocks[] = [
                                            'type' => 'custom-html-block',
                                            'data' => [
                                                'content' => $generatedHtml,
                                            ],
                                        ];
                                    } else {
                                        $blocks[] = [
                                            'type' => 'custom-html-block',
                                            'data' => [
                                                'content' => __('Design-to-Code generation is not yet implemented.'),
                                            ],
                                        ];
                                    }
                                    $set('blocks', $blocks);
                                })
                                ->form([
                                    Forms\Components\Select::make('generation_type')
                                        ->label(__('Generation Type'))
                                        ->options([
                                            'text' => __('Text-to-HTML'),
                                            'design' => __('Design-to-Code'),
                                        ])
                                        ->default('text')
                                        ->reactive(),
                                    Forms\Components\Textarea::make('prompt')
                                        ->label(__('Enter a prompt to generate a component'))
                                        ->required()
                                        ->visible(fn ($get) => $get('generation_type') === 'text'),
                                    Forms\Components\FileUpload::make('design_file')
                                        ->label(__('Upload a design file'))
                                        ->required()
                                        ->visible(fn ($get) => $get('generation_type') === 'design'),
                                ]),
                        ]),

                        Group::make()->schema(FilamentFabricator::getSchemaSlot('blocks.after')),
                    ])
                    ->columnSpan(2),

                Group::make()
                    ->columnSpan(1)
                    ->schema([
                        Group::make()->schema(FilamentFabricator::getSchemaSlot('sidebar.before')),
                        Section::make()
                            ->schema([
                                Placeholder::make('page_url')
                                    ->visible(fn (?PageContract $record) => config('filament-fabricator.routing.enabled') && filled($record))
                                    ->content(fn (?PageContract $record) => FilamentFabricator::getPageUrlFromId($record?->id)),

                                    TextInput::make('title')
                                    ->label(__('filament-fabricator::page-resource.labels.title'))
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $state, ?PageContract $record) {
                                        if (! $get('is_slug_changed_manually') && filled($state) && blank($record)) {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->debounce('500ms')
                                    ->required(),

                                Hidden::make('is_slug_changed_manually')
                                    ->default(false)
                                    ->dehydrated(false),

                                TextInput::make('slug')
                                    ->label(__('filament-fabricator::page-resource.labels.slug'))
                                    ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('parent_id', $get('parent_id')))
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('is_slug_changed_manually', true);
                                    })
                                    ->rule(function ($state) {
                                        return function (string $attribute, $value, Closure $fail) use ($state) {
                                            if ($state !== '/' && (Str::startsWith($value, '/') || Str::endsWith($value, '/'))) {
                                                $fail(__('filament-fabricator::page-resource.errors.slug_starts_or_ends_with_slash'));
                                            }
                                        };
                                    })
                                    ->required(),

                                TagsInput::make('seo_tags')->label('SEO Tags')->required(),

                                Select::make('layout')
                                    ->label(__('filament-fabricator::page-resource.labels.layout'))
                                    ->options(FilamentFabricator::getLayouts())
                                    ->default('default')
                                    ->required(),

                                Select::make('parent_id')
                                    ->label(__('filament-fabricator::page-resource.labels.parent'))
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->suffixAction(
                                        fn ($get, $context) => FormAction::make($context . '-parent')
                                                ->icon('heroicon-o-arrow-top-right-on-square')
                                                ->url(fn () => $get('parent_id')!=null ? PageResource::getUrl($context, ['record' => $get('parent_id')]):null)
                                                ->openUrlInNewTab()
                                                ->visible(fn () => filled($get('parent_id')))
                                    )
                                    ->relationship(
                                        'parent',
                                        'title',
                                        function (Builder $query, ?PageContract $record) {
                                            if (filled($record)) {
                                                $query->where('id', '!=', $record->id);
                                            }
                                        }
                                    ),
                                Textarea::make('meta_description')
                                   ->label('Meta Description')
                                   ->required(),
                                Toggle::make('published')
                                   ->label('Publish Page')
                                   ->disabledOn('create'),
                                Repeater::make('viewable_by')
                                    ->schema([
                                        Select::make('users')
                                        ->label('Select users')
                                        ->multiple()
                                        ->searchable()
                                        ->preload()
                                        ->options(function(){
                                            // $users = DB::table('model_has_roles')
                                            //         ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                                            //         ->where('roles.name', '!=', 'super_admin')
                                                    
                                            //         ->get();

                                                 $users = DB::table('model_has_roles')
                                                 ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                                                 ->join('users', 'model_has_roles.model_id', '=', 'users.id')
                                                 ->where('roles.name', '!=', 'super_admin')
                                                 ->select('users.*')
                                                 ->get();
                                                 
                                            return $users->pluck('name','id');
                                        }),
                                    ])
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderableWithDragAndDrop(false)
                            ]),
                        Group::make()->schema(FilamentFabricator::getSchemaSlot('sidebar.after')),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                ->label(__('filament-fabricator::page-resource.labels.title'))
                ->searchable()
                ->sortable(),

            TextColumn::make('url')
                ->label(__('filament-fabricator::page-resource.labels.url'))
                ->toggleable()
                ->getStateUsing(fn (?PageContract $record) => FilamentFabricator::getPageUrlFromId($record->id) ?: null)
                ->url(fn (?PageContract $record) => FilamentFabricator::getPageUrlFromId($record->id) ?: null, true)
                ->visible(config('filament-fabricator.routing.enabled')),

            TextColumn::make('layout')
                ->badge()
                ->label(__('filament-fabricator::page-resource.labels.layout'))
                ->toggleable()
                ->sortable(),
                //->enum(FilamentFabricator::getLayouts()),

            TextColumn::make('parent.title')
                ->label(__('filament-fabricator::page-resource.labels.parent'))
                ->toggleable(isToggledHiddenByDefault: true)
                ->formatStateUsing(fn ($state) => $state ?? '-')
                ->url(fn (?PageContract $record) => filled($record->parent_id) ? PageResource::getUrl('edit', ['record' => $record->parent_id]) : null),

            IconColumn::make('published')->icons([
                'heroicon-o-x-circle' => fn($state, $record): bool => $record->published ==false,
                'heroicon-o-check-circle' => fn($state, $record): bool => $record->published ==true,
            ])
            ->colors([
                'danger'=> fn($state, $record): bool => $record->published ==false,
                'success' => fn($state, $record): bool => $record->published ==true
            ])->extraAttributes(function($record){
                if($record->published)
                {
                    return ['title'=>'published'];
                }
                else
                {
                    return ['title'=>'unpublished'];
                }
            }),
            TextColumn::make('created_by')
                ->label('Created By')
                ->getStateUsing(function(Page $record){
                if($record->created_by !=null)
                {
                    return $record->who_created->name;
                }
                else
                {
                    return "NULL";
                }
           })
            ])
            ->filters([
                SelectFilter::make('layout')
                ->label(__('filament-fabricator::page-resource.labels.layout'))
                ->options(FilamentFabricator::getLayouts()),
            ])
            ->actions([
                ViewAction::make()
                ->visible(config('filament-fabricator.enable-view-page')),
                Action::make('preview')
                ->color('success')
                ->icon('heroicon-s-eye')
                ->url(function(Page $record){
                    return route('preview-page',[$record,'status'=>1]);
                })
                ->openUrlInNewTab(),
                EditAction::make(),
                Action::make('visit')
                    ->label(__('filament-fabricator::page-resource.actions.visit'))
                    ->url(fn (?PageContract $record) => FilamentFabricator::getPageUrlFromId($record->id, true) ?: null)
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->openUrlInNewTab()
                    ->color('success')
                    ->visible(config('filament-fabricator.routing.enabled')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\Action::make('Export Pages')->label('Export')
                        ->action(function(){
                            $this->export();
                        })
                        ->button()
                        ->color('success')
                        ->icon('heroicon-o-arrow-up-on-square'),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public function export()
    {
       dd("HEree");
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
            'view' => Pages\EditPage::route('/{record}/view'),
        ];
    }
}
