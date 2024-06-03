<?php

namespace App\Http\Livewire;

use App\Http\Controllers\Features\UnitController;
use Livewire\Component;
use Illuminate\Support\Facades\Session;
use App\Models\{Agent, Unit};

class CalendarIndexComponent extends Component
{
    public $unitUuid;

    public function render()
    {
        if(!$this->unitUuid){
            $units = Unit::where('property_uuid',Session::get('property_uuid'))->where('rent_duration', UnitController::DAILY)->get();
        }else{
            $units = Unit::where('uuid', $this->unitUuid)->get();
        }

        $guestCreateRestriction = app('App\Http\Controllers\Utilities\UserRestrictionController')->isFeatureAuthorized(7, 1);

        return view('livewire.features.calendar.calendar-index-component',[
            'agents' => Agent::where('property_uuid', Session::get('property_uuid'))->where('is_active',1)->get(),
            'units' => $units,
            'property' => Session::get('property_uuid'),
            'unit' => $this->unitUuid?Unit::where('uuid', $this->unitUuid)->value('unit'):"",
            'guestCreateRestriction' => $guestCreateRestriction
        ]);
    }
}
