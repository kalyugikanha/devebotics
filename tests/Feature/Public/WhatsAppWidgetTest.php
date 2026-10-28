<?php

namespace Tests\Feature\Public;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_widget_links_to_the_configured_number_with_prefilled_messages(): void
    {
        settings()->setMany('contact', [
            'whatsapp_number' => '+44 7700 900123',
            'whatsapp_quick_replies' => ['I need a quote & a timeline'],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('id="whatsapp-panel"', false)
            ->assertSee('https://wa.me/447700900123?text=I%20need%20a%20quote%20%26%20a%20timeline', false);
    }

    public function test_widget_is_hidden_in_production_until_a_number_is_configured(): void
    {
        $this->app['env'] = 'production';

        $this->get('/')
            ->assertOk()
            ->assertDontSee('id="whatsapp-panel"', false)
            ->assertDontSee('wa.me', false);
    }

    public function test_widget_shows_a_preview_without_a_link_outside_production(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('id="whatsapp-panel"', false)
            ->assertDontSee('wa.me', false);
    }

    public function test_widget_can_be_switched_off(): void
    {
        settings()->setMany('contact', ['whatsapp_number' => '+447700900123', 'whatsapp_widget_enabled' => false]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('id="whatsapp-panel"', false);
    }
}
