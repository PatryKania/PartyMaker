<?php

namespace App\Filament\EventPanel\Resources\Surveys\Pages;

use App\Filament\EventPanel\Resources\Surveys\SurveyResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSurvey extends ViewRecord
{
    protected static string $resource = SurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
