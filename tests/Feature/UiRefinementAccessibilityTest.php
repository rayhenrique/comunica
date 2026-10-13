<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\Coordinator\CreateRequest;
use App\Models\User;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UiRefinementAccessibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_help_and_errors_reference_existing_elements_before_and_after_validation(): void
    {
        $this->actingAs(User::factory()->create());
        $component = Livewire::test(CreateRequest::class);
        $this->assertDescriptionReferences($component->html());
        $component->set('form.title', 'Valores preservados')->call('save')
            ->assertHasErrors('form.start_at')->assertSet('form.title', 'Valores preservados');
        $this->assertDescriptionReferences($component->html());
        $component->set('form.start_at', now()->addDays(4)->format('Y-m-d\TH:i'))
            ->assertHasNoErrors('form.start_at');
        $this->assertDescriptionReferences($component->html());
        $this->assertDatabaseCount('event_requests', 0);
    }

    private function assertDescriptionReferences(string $html): void
    {
        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        try {
            $document->loadHTML('<?xml encoding="UTF-8">'.$html);
            $xpath = new DOMXPath($document);
            foreach ($xpath->query('//*[@aria-describedby]') as $element) {
                foreach (explode(' ', $element->getAttribute('aria-describedby')) as $id) {
                    $this->assertNotNull($document->getElementById($id), 'Descrição ARIA ausente: '.$id);
                }
            }
            foreach ($xpath->query('//*[@aria-invalid="true"]') as $element) {
                $this->assertStringContainsString('-error', $element->getAttribute('aria-describedby'));
            }
            $this->assertSame(0, $xpath->query('//*[contains(@class,"field-help") and not(normalize-space())]')->length);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }
}
