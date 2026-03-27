<?php

namespace App\Filament\EventPanel\Resources\Participants\Pages;

use App\Filament\EventPanel\Resources\Participants\ParticipantResource;
use App\Models\Participant;
use Filament\Resources\Pages\CreateRecord;
use App\Models\User;

class CreateParticipant extends CreateRecord
{
    protected static string $resource = ParticipantResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['email'])) {
            $user = User::where('email', $data['email'])->first();

            if ($user) {
                $data['user_id'] = $user->id;
            }
        }


        if(!empty($data['related_id'])){
            $currentUser = Participant::where('id', $data['related_id'])->first();

            if(empty($data['first_name'])){
                $data['first_name'] = "Osoba towarzycząca";
                $data['last_name'] = '('.$currentUser->first_name." ".$currentUser->last_name.')';
            }else{
                 if(empty($data['last_name'])){
                $data['last_name'] = '('.$currentUser->first_name." ".$currentUser->last_name.')';
            }
            }

            
           
        }

        return $data;
    }

    
}
