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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use App\Filament\EventPanel\Resources\Memories\Schemas\MemoryForm;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Actions\ViewAction;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class ManageMemories extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $resource = MemoryResource::class;
    protected string $view = 'filament.event-panel.resources.memories.pages.manage-memories';
    protected static ?string $title = 'Create memories';

    public ?array $data = [];

    public static function table(Table $table): Table
    {
        return $table
            ->query(Memory::query()->latest())
            ->columns([
                Split::make([
                    Stack::make([
                        ImageColumn::make('images')
                            ->label('Zdjęcia')
                            ->disk('public')
                            ->getStateUsing(
                                fn($record) =>
                                $record->memoryMedia
                                    ->where('type', 'image')
                                    ->pluck('path')
                                    ->toArray()
                            )
                            ->stacked()
                            ->limit(3),
                        TextColumn::make('desc')->label('Opis'),
                    ])
                ])

            ])
            ->filters([
                //
            ])->actions([
                ViewAction::make()
                    ->label('Podgląd')
                    ->icon(null)
                    ->label('')
                    ->modalContent(fn($record) => new HtmlString(Blade::render(<<<'BLADE'
        <div class="memory-wrapper">
            {{-- Slider --}}
            <div class="memory-slider">
             @foreach($record->memoryMedia as $media)
                    <div class="memory-slide">
                        
                        @if($media->type === 'image')
                            <img src="{{ Storage::disk('public')->url($media->path) }}" alt="Zdjęcie">
                        
                        @elseif($media->type === 'video')
                            <video controls playsinline>
                                <source src="{{ Storage::disk('public')->url($media->path) }}" type="video/mp4">
                                Twój nie obsługuje elementu wideo.
                            </video>
                        @endif

                    </div>
                @endforeach
            </div>

            {{-- Opis --}}
            @if($record->desc)
                <div class="memory-desc">
                    {{ $record->desc }}
                </div>
            @endif
        </div>
    BLADE, ['record' => $record])))
            ])
            ->recordAction('view')
            ->recordActions([])
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

        if (!empty($data['images'])) {
            foreach ($data['images'] as $image) {
                $memory->memoryMedia()->create([
                    'type' => 'image',
                    'path' => $image,
                ]);
            }
        }

        if (!empty($data['video'])) {
            $memory->memoryMedia()->create([
                'type' => 'video',
                'path' => $data['video'],
            ]);
        }
        $this->form->fill([]);

        Notification::make()
            ->title('Pomyślnie utworzono wspomnienie!')
            ->success()
            ->send();

        $this->dispatch('refresh');
    }

    public function getTitle(): string
    {
        return __('Create memories');
    }
}
