<?php

namespace App\Filament\EventPanel\Resources\Surveys\Pages;

use App\Filament\EventPanel\Resources\Surveys\SurveyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSurvey extends CreateRecord
{
    protected static string $resource = SurveyResource::class;
}
