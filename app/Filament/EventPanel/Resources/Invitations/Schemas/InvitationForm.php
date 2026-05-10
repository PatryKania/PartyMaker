<?php

namespace App\Filament\EventPanel\Resources\Invitations\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Radio;
use Illuminate\Support\HtmlString;

class InvitationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([

                Radio::make('theme')
                    ->label(__('Theme'))
                    ->options([
                        'classic' => __('Classic'),
                        'modern' => __('Modern'),
                    ])
                    ->descriptions([
                        'classic' => new HtmlString('<img src="/images/templates/classic.png" style="height: 250px; border-radius: 8px; margin-top: 8px; border: 1px solid #e5e7eb;">'),
                        'modern' => new HtmlString('<img src="/images/templates/modern.png" style="height: 250px; border-radius: 8px; margin-top: 8px; border: 1px solid #e5e7eb;">'),
                    ])
                    ->columns(2)
                    ->required(),

                RichEditor::make('content')
                    ->label(__('Content'))
                    ->required()
                    ->translatableTabs([
                        'pl' => 'Polski',
                        'en' => 'English',
                    ]),
            ]);
    }
}
