<?php

namespace App\Filament\EventPanel\Resources\Participants\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use App\Enums\ParticipantRole;
use App\Enums\ParticipantType;
// use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;

class ParticipantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')->label(__('First name'))->required(),
                TextInput::make('last_name')->label(__('Last name'))->required(),

                Select::make('type')->label(__('Type'))
                    ->options(ParticipantType::class)->default('adult')->required()->native(false)->live(),


                Select::make('parent_ids')
                    ->label(__('Parents'))
                    ->relationship('parents', 'last_name')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->first_name} {$record->last_name}")
                    ->searchable()
                    ->multiple()
                    ->maxItems(2)
                    ->preload()
                    ->visible(fn($get) => $get('type') === ParticipantType::Child)->live(),

                Select::make('role')->label(__('Role'))
                    ->options(ParticipantRole::class)->default('guest')->required()->native(false),

                TextInput::make('email')->label(__('E-mail'))->required()->email()
                    ->required(fn($get) => $get('type') !== ParticipantType::Child)->unique(
                        table: 'participants',
                        column: 'email',
                        ignorable: fn($record) => $record,
                        modifyRuleUsing: function ($rule, $get) {
                            return $rule->where('event_id', $get('event_id'));
                        }
                    ),
                TextInput::make('phone')->label(__('Phone'))->tel()->prefix('+48')->mask('999-999-999')
                    ->unique(
                        table: 'participants',
                        column: 'phone',
                        ignorable: fn($record) => $record,
                        modifyRuleUsing: function ($rule, $get) {
                            return $rule->where('event_id', $get('event_id'));
                        }
                    ),



            ]);
    }
}
