<?php

namespace App\Filament\Resources\AdminUsers\Schemas;

use App\Models\AdminUser;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class AdminUserForm
{
    // Deliberately excludes otp_code / otp_expires_at / otp_sent_count /
    // mobile_verified_at — those are internal sign-in state, not something
    // any admin should be able to hand-edit (setting a known OTP for
    // another account here would be a full account-takeover hole).
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('mobile')
                    ->label('Mobile number')
                    ->required()
                    ->numeric()
                    ->minLength(10)
                    ->maxLength(15)
                    ->unique(ignoreRecord: true)
                    // Normalized live (not just on save) so the uniqueness
                    // check above compares like-for-like — "0801..." and an
                    // already-registered "234801..." are the same number.
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('mobile', AdminUser::normalizeMobile((string) $state)))
                    ->dehydrateStateUsing(fn (?string $state) => AdminUser::normalizeMobile((string) $state))
                    ->placeholder('e.g. 08012345678')
                    ->helperText('You can type it starting with 0 or 234 — this is what they sign in with.'),
                Select::make('role')
                    ->options([
                        'editor' => 'Editor',
                        'admin' => 'Admin',
                    ])
                    ->default('editor')
                    ->required()
                    ->native(false)
                    ->helperText('Admins can manage every post, category, tag, and team member. Editors can only manage their own posts.'),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->helperText('Turn off to immediately block this person from signing in, without deleting their account.'),
            ]);
    }
}
