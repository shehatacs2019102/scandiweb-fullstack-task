<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GraphQLApiTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function graphQL(string $query): array
    {
        return $this->postJson('/graphql', ['query' => $query])
            ->assertOk()
            ->assertJsonMissingPath('errors')
            ->json('data');
    }

    public function test_it_lists_categories(): void
    {
        $data = $this->graphQL('{ getCategories { name } }');

        $this->assertSame(['all', 'clothes', 'tech'], array_column($data['getCategories'], 'name'));
    }

    public function test_it_groups_products_by_category(): void
    {
        $data = $this->graphQL('{ getProducts {
            tech { id category { name } }
            clothes { id category { name } }
        } }');

        $this->assertNotEmpty($data['getProducts']['tech']);
        $this->assertNotEmpty($data['getProducts']['clothes']);
        $this->assertSame(['tech'], array_unique(array_column(array_column($data['getProducts']['tech'], 'category'), 'name')));
        $this->assertSame(['clothes'], array_unique(array_column(array_column($data['getProducts']['clothes'], 'category'), 'name')));
    }

    public function test_it_fetches_a_product_by_id(): void
    {
        $product = $this->graphQL('{ getProductById(id: "huarache-x-stussy-le") {
            id name amount currency_symbol gallery in_stock attributes { id name value }
        } }')['getProductById'];

        $this->assertSame('Nike Air Huarache Le', $product['name']);
        $this->assertSame(144.69, $product['amount']);
        $this->assertSame('$', $product['currency_symbol']);
        $this->assertCount(5, json_decode($product['gallery'], true));
        $this->assertSame(['40', '41', '42', '43'], array_column($product['attributes'], 'value'));
    }

    public function test_unknown_product_returns_null(): void
    {
        $this->assertNull($this->graphQL('{ getProductById(id: "nope") { id } }')['getProductById']);
    }

    public function test_it_creates_an_order(): void
    {
        $order = $this->graphQL('mutation { createOrder(items: "Nike x 1", total_price: 144.69) { id items total_price } }')['createOrder'];

        $this->assertSame(144.69, $order['total_price']);
        $this->assertDatabaseHas('orders', ['id' => $order['id'], 'items' => 'Nike x 1']);
    }
}
