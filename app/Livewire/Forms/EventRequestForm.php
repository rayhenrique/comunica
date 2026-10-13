<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Http\Requests\CreateEventRequestRequest;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EventRequestForm extends Form
{
    #[Validate]
    public string $title = '';
    #[Validate]
    public string $demand_type = 'coverage';
    #[Validate]
    public string $start_at = '';
    #[Validate]
    public int|string $duration_minutes = 60;
    #[Validate]
    public string $location = '';
    #[Validate]
    public string $target_audience = '';
    #[Validate]
    public string $description = '';

    /** @return array<string, array<int, mixed>> */
    protected function rules(): array
    {
        return (new CreateEventRequestRequest())->rules();
    }

    /** @return array<string, string> */
    protected function validationAttributes(): array
    {
        return (new CreateEventRequestRequest())->attributes();
    }
}
