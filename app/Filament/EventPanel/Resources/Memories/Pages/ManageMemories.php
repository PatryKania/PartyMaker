<?php

namespace App\Filament\EventPanel\Resources\Memories\Pages;

use App\Filament\EventPanel\Resources\Memories\MemoryResource;
use App\Models\Memory;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Resources\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use App\Filament\EventPanel\Resources\Memories\Schemas\MemoryForm;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;

class ManageMemories extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $resource = MemoryResource::class;
    protected string $view = 'filament.event-panel.resources.memories.pages.manage-memories';

    public ?array $data = [];

    public static function table(Table $table): Table
    {
        return $table
            ->query(Memory::query()->latest())
            ->columns([
                TextColumn::make('desc')->label('Opis'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }


    public function mount(): void
    {
        $this->form->fill();
    }


    public static function form(Schema $schema): Schema
    {
        return MemoryForm::configure($schema);
    }

    public function create()
    {
        $data = $this->form->getState();
        $data['user_id'] = auth()->id();
        $data['event_id'] = filament()->getTenant()->id;
        $memory = Memory::create($data);

        $this->form->fill([]);

        Notification::make()
            ->title('Pomyślnie utworzono wspomnienie!')
            ->success()
            ->send();

        $this->dispatch('refresh');
    }
}
