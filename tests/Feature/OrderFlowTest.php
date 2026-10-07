<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        // MySQL dipakai karena pdo_sqlite tidak tersedia di mesin ini.
        // Env harus diset SEBELUM app boot (parent::setUp).
        putenv('DB_CONNECTION=mysql');
        putenv('DB_DATABASE=tasty_food_testing');
        $_ENV['DB_CONNECTION'] = 'mysql';
        $_ENV['DB_DATABASE'] = 'tasty_food_testing';
        $_SERVER['DB_CONNECTION'] = 'mysql';
        $_SERVER['DB_DATABASE'] = 'tasty_food_testing';
        parent::setUp();
    }

    private function makeMenu(array $over = []): Menu
    {
        return Menu::create(array_merge([
            'name' => 'Shoyu Ramen',
            'slug' => 'shoyu-'.uniqid(),
            'price' => 38000,
            'is_available' => true,
        ], $over));
    }

    private function makeAdmin(): User
    {
        return User::factory()->create();
    }

    public function test_checkout_creates_order_with_server_side_total(): void
    {
        $menu = $this->makeMenu();

        // Coba tipu total via browser: server harus hitung ulang.
        $res = $this->withSession(['cart' => [$menu->id => 2]])
            ->post('/checkout', [
                'customer_name' => 'Budi',
                'customer_phone' => '08123456789',
                'customer_address' => 'Jl Asia Afrika 1',
            ]);

        $res->assertRedirect();
        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertMatchesRegularExpression('/^KAIRO-\d{4,}$/', $order->order_code);
        $this->assertSame('pending', $order->status);
        $this->assertSame(76000, (int) $order->total_amount);
        $this->assertSame(2, (int) $order->items()->first()->quantity);
        $this->assertSame('Shoyu Ramen', $order->items()->first()->menu_name);
        // Cart dikosongkan.
        $this->assertEmpty(session('cart', []));
    }

    public function test_unavailable_menu_blocked_at_checkout(): void
    {
        $menu = $this->makeMenu(['is_available' => false]);

        $res = $this->withSession(['cart' => [$menu->id => 1]])
            ->post('/checkout', [
                'customer_name' => 'Budi',
                'customer_phone' => '08123456789',
                'customer_address' => 'Jl Test',
            ]);

        $res->assertRedirect(route('cart.index'));
        $this->assertSame(0, Order::count());
    }

    public function test_empty_cart_blocked(): void
    {
        $res = $this->post('/checkout', [
            'customer_name' => 'Budi',
            'customer_phone' => '08123456789',
            'customer_address' => 'Jl Test',
        ]);

        $res->assertRedirect(route('cart.index'));
        $this->assertSame(0, Order::count());
    }

    public function test_full_admin_flow_to_completed(): void
    {
        $admin = $this->makeAdmin();
        $menu = $this->makeMenu();

        $this->withSession(['cart' => [$menu->id => 1]])
            ->post('/checkout', [
                'customer_name' => 'Sinta',
                'customer_phone' => '089999',
                'customer_address' => 'Jl Merdeka 5',
            ]);

        $order = Order::first();

        // approve
        $this->actingAs($admin)->post(route('admin.orders.approve', $order));
        $this->assertSame('approved', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->approved_at);

        // advance bertahap: cooking -> ready -> delivering -> delivered
        foreach (['cooking', 'ready', 'delivering', 'delivered'] as $expected) {
            $this->actingAs($admin)->post(route('admin.orders.advance', $order));
            $this->assertSame($expected, $order->fresh()->status);
        }
        $this->assertNotNull($order->fresh()->delivered_at);

        // advance lagi harus ditolak (tidak ada status berikutnya)
        $this->actingAs($admin)->post(route('admin.orders.advance', $order));
        $this->assertSame('delivered', $order->fresh()->status);

        // customer konfirmasi
        $this->post(route('orders.confirm', $order->order_code));
        $this->assertSame('completed', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->completed_at);
    }

    public function test_reject_flow(): void
    {
        $admin = $this->makeAdmin();
        $menu = $this->makeMenu();

        $this->withSession(['cart' => [$menu->id => 1]])
            ->post('/checkout', [
                'customer_name' => 'Rudi',
                'customer_phone' => '08111',
                'customer_address' => 'Jl Test',
            ]);

        $order = Order::first();
        $this->actingAs($admin)->post(route('admin.orders.reject', $order));

        $this->assertSame('rejected', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->rejected_at);

        // approve setelah reject harus ditolak
        $this->actingAs($admin)->post(route('admin.orders.approve', $order));
        $this->assertSame('rejected', $order->fresh()->status);
    }

    public function test_confirm_rejected_when_not_delivered(): void
    {
        $menu = $this->makeMenu();
        $this->withSession(['cart' => [$menu->id => 1]])
            ->post('/checkout', [
                'customer_name' => 'Dewi',
                'customer_phone' => '08222',
                'customer_address' => 'Jl Test',
            ]);

        $order = Order::first();
        $this->post(route('orders.confirm', $order->order_code));
        // masih pending, tidak berubah
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_auto_complete_after_one_hour(): void
    {
        $menu = $this->makeMenu();
        $old = Order::create([
            'order_code' => 'KAIRO-9001',
            'customer_name' => 'Lupa',
            'customer_phone' => '08333',
            'customer_address' => 'Jl Test',
            'total_amount' => $menu->price,
            'status' => 'delivered',
            'delivered_at' => now()->subMinutes(61),
        ]);
        $fresh = Order::create([
            'order_code' => 'KAIRO-9002',
            'customer_name' => 'Baru',
            'customer_phone' => '08444',
            'customer_address' => 'Jl Test',
            'total_amount' => $menu->price,
            'status' => 'delivered',
            'delivered_at' => now()->subMinutes(30),
        ]);
        $done = Order::create([
            'order_code' => 'KAIRO-9003',
            'customer_name' => 'Selesai',
            'customer_phone' => '08555',
            'customer_address' => 'Jl Test',
            'total_amount' => $menu->price,
            'status' => 'completed',
            'completed_at' => now()->subHours(2),
        ]);

        $this->artisan('orders:auto-complete')->assertSuccessful();

        $this->assertSame('completed', $old->fresh()->status);
        $this->assertNotNull($old->fresh()->completed_at);
        // belum 1 jam: tetap delivered
        $this->assertSame('delivered', $fresh->fresh()->status);
        // sudah completed: tidak berubah (idempotent)
        $this->assertSame('completed', $done->fresh()->status);

        // jalan kedua kali: tetap aman
        $this->artisan('orders:auto-complete')->assertSuccessful();
        $this->assertSame('completed', $old->fresh()->status);
    }

    public function test_admin_routes_require_auth(): void
    {
        $this->get(route('admin.orders.index'))->assertRedirect();
    }

    public function test_cart_qty_min_one(): void
    {
        $menu = $this->makeMenu();

        // qty 0 ditolak validasi
        $this->withSession(['cart' => [$menu->id => 1]])
            ->patch(route('cart.update', $menu), ['qty' => 0])
            ->assertSessionHasErrors('qty');
    }
}
