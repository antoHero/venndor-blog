<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Enums\PostStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Section::make([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->alphaDash()
                            ->helperText('Used in the post URL. Auto-filled from the title — you can override it.'),
                        Textarea::make('excerpt')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Shown on the blog listing and in social previews.')
                            ->columnSpanFull(),
                        RichEditor::make('body')
                            ->required()
                            ->fileAttachmentsDisk(config('filesystems.media_disk', 'public'))
                            ->fileAttachmentsDirectory('posts/attachments')
                            ->fileAttachmentsVisibility('public')
                            ->columnSpanFull(),
                    ]),

                    Section::make('SEO')
                        ->description('Optional overrides for search engines and social sharing.')
                        ->collapsible()
                        ->collapsed()
                        ->components([
                            TextInput::make('seo_title')
                                ->maxLength(60)
                                ->helperText('Defaults to the title if left blank. Aim for 60 characters.'),
                            Textarea::make('seo_description')
                                ->rows(2)
                                ->maxLength(160)
                                ->helperText('Defaults to the excerpt if left blank. Aim for 160 characters.'),
                        ]),
                ])->columnSpan(2),

                Group::make([
                    Section::make('Publishing')
                        ->components([
                            Select::make('status')
                                ->options(PostStatus::class)
                                ->default(PostStatus::Draft)
                                ->required()
                                ->live()
                                ->native(false),
                            DateTimePicker::make('published_at')
                                ->label('Publish at')
                                ->native(false)
                                ->default(now())
                                ->required(fn (Get $get): bool => in_array($get('status'), [
                                    PostStatus::Scheduled->value,
                                    PostStatus::Published->value,
                                ]))
                                ->visible(fn (Get $get): bool => in_array($get('status'), [
                                    PostStatus::Scheduled->value,
                                    PostStatus::Published->value,
                                ])),
                            Select::make('admin_user_id')
                                ->label('Author')
                                ->relationship('author', 'name')
                                ->default(fn () => auth('admin')->id())
                                ->required()
                                ->searchable()
                                ->preload()
                                ->disabled(fn (): bool => ! auth('admin')->user()?->isAdmin())
                                ->dehydrated(),
                        ]),

                    Section::make('Organize')
                        ->components([
                            Select::make('category_id')
                                ->label('Category')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                    TextInput::make('slug')
                                        ->required()
                                        ->unique('categories', 'slug'),
                                ]),
                            Select::make('tags')
                                ->relationship('tags', 'name')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                    TextInput::make('slug')
                                        ->required()
                                        ->unique('tags', 'slug'),
                                ]),
                        ]),

                    Section::make('Featured image')
                        ->components([
                            SpatieMediaLibraryFileUpload::make('featured_image')
                                ->collection('posts')
                                ->hiddenLabel()
                                ->image()
                                ->imageEditor()
                                ->disk(config('filesystems.media_disk', 'public'))
                                ->visibility('public')
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->maxSize(5120),
                        ]),
                ])->columnSpan(1),
            ]);
    }
}
