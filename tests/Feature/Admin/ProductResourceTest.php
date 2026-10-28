<?php

namespace Tests\Feature\Admin;

use App\Enums\BillingPeriod;
use App\Enums\ContentStatus;
use App\Enums\PricingModel;
use App\Enums\ProductAvailability;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\RelationManagers\PlansRelationManager;
use App\Models\Media;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_manager_can_list_create_and_edit_products(): void
    {
        $user = User::factory()->withRole('Product Manager')->create();
        $existing = Product::factory()->create(['name' => 'Existing Suite']);
        $category = ProductCategory::factory()->create();

        $this->actingAs($user);

        Livewire::test(ListProducts::class)->assertOk()->assertCanSeeTableRecords([$existing]);

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'Nova Analytics',
                'slug' => 'nova-analytics',
                'summary' => 'Self-service analytics for growing teams.',
                'product_category_id' => $category->id,
                'pricing_model' => PricingModel::Plans->value,
                'availability' => ProductAvailability::Beta->value,
                'status' => ContentStatus::Published->value,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $product = Product::query()->where('slug', 'nova-analytics')->firstOrFail();
        $this->assertSame(ProductAvailability::Beta, $product->availability);
        $this->assertSame($category->id, $product->product_category_id);

        Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
            ->assertOk()
            ->fillForm(['tagline' => 'Insights without the wait'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Insights without the wait', $product->refresh()->tagline);
    }

    public function test_gallery_items_are_saved_with_the_gallery_collection(): void
    {
        $user = User::factory()->withRole('Product Manager')->create();
        $product = Product::factory()->create();
        $media = Media::factory()->create();

        $this->actingAs($user);

        Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
            ->fillForm(['galleryItems' => [['media_id' => $media->id, 'caption' => 'Dashboard view']]])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('media_attachments', [
            'attachable_type' => 'product',
            'attachable_id' => $product->id,
            'media_id' => $media->id,
            'collection' => 'gallery',
            'caption' => 'Dashboard view',
        ]);
    }

    public function test_plans_can_be_managed_through_the_relation_manager(): void
    {
        $user = User::factory()->withRole('Product Manager')->create();
        $product = Product::factory()->create(['pricing_model' => PricingModel::Plans]);

        $this->actingAs($user);

        Livewire::test(PlansRelationManager::class, ['ownerRecord' => $product, 'pageClass' => EditProduct::class])
            ->assertOk()
            ->callAction(TestAction::make(CreateAction::class)->table(), [
                'name' => 'Team',
                'price' => 49,
                'currency' => 'USD',
                'billing_period' => BillingPeriod::Monthly->value,
                'features' => ['Up to 10 users', 'Email support'],
                'is_active' => true,
            ])
            ->assertHasNoFormErrors();

        $plan = $product->plans()->firstOrFail();
        $this->assertSame('Team', $plan->name);
        $this->assertSame(['Up to 10 users', 'Email support'], $plan->features);
        $this->assertSame('$49', $plan->formattedPrice());
    }

    public function test_users_without_product_permissions_cannot_access_products(): void
    {
        $user = User::factory()->withRole('HR Manager')->create();

        $this->actingAs($user)->get('/admin/products')->assertForbidden();
    }

    public function test_seo_manager_can_view_but_not_create_products(): void
    {
        $user = User::factory()->withRole('SEO Manager')->create();

        $this->actingAs($user)->get('/admin/products')->assertOk();
        $this->actingAs($user)->get('/admin/products/create')->assertForbidden();
    }
}
