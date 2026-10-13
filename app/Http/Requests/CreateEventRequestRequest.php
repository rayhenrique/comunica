<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\EventRequest;
use App\Rules\MinimumEventLeadTime;
use App\Rules\FitsTextColumn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CreateEventRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', EventRequest::class);
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'demand_type' => ['required', Rule::in(['coverage', 'art', 'both'])],
            'start_at' => ['bail', 'required', 'string', 'date', new MinimumEventLeadTime()],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:65535'],
            'location' => ['required', 'string', 'max:255'],
            'target_audience' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', new FitsTextColumn()],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'title' => 'título', 'demand_type' => 'tipo de demanda', 'start_at' => 'data e hora de início',
            'duration_minutes' => 'duração em minutos', 'location' => 'local',
            'target_audience' => 'público-alvo', 'description' => 'briefing',
        ];
    }
}
