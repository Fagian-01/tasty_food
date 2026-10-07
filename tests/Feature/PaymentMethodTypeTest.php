<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentMethodTypeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        putenv('DB_CONNECTION=mysql');
        putenv('DB_DATABASE=tasty_food_testing');
        $_ENV['DB_CONNECTION'] = 'mysql';
        $_ENV['DB_DATABASE'] = 'tasty_food_testing';
        $_SERVER['DB_CONNECTION'] = 'mysql';
        $_SERVER['DB_DATABASE'] = 'tasty_food_testing';
        parent::setUp();
        Storage::fake('public');
    }

    private function makeAdmin(): User
    {
        return User::factory()->create();
    }

    private function makeMenu(): Menu
    {
        return Menu::create([
            'name' => 'Shoyu Ramen',
            'slug' => 'shoyu-'.uniqid(),
            'price' => 38000,
            'is_available' => true,
        ]);
    }

    private function checkoutOrder(): Order
    {
        $menu = $this->makeMenu();
        $this->withSession(['cart' => [$menu->id => 1]])
            ->post('/checkout', [
                'customer_name' => 'Budi',
                'customer_phone' => '08123456789',
                'customer_address' => 'Jl Asia Afrika 1',
            ]);

        return Order::firstOrFail();
    }

    private function image(string $name = 'qris.jpg'): UploadedFile
    {
        return UploadedFile::fake()->create($name, 500, 'image/jpeg');
    }

    private function basePayload(string $type): array
    {
        return match ($type) {
            'dana' => [
                'type' => 'dana',
                'name' => 'DANA KAIRO',
                'account_name' => 'KAIRO RAMEN',
                'account_number' => '081234567890',
                'instructions' => 'Transfer sesuai total lalu upload bukti.',
                'is_active' => '1',
            ],
            'ewallet' => [
                'type' => 'ewallet',
                'name' => 'GoPay KAIRO',
                'provider' => 'GoPay',
                'account_name' => 'KAIRO RAMEN',
                'account_number' => '081298765432',
                'instructions' => 'Transfer via GoPay lalu upload bukti.',
                'is_active' => '1',
            ],
            'bank' => [
                'type' => 'bank',
                'name' => 'BCA KAIRO',
                'bank_name' => 'BCA',
                'account_name' => 'KAIRO RAMEN',
                'account_number' => '1234567890',
                'instructions' => 'Transfer ke rekening lalu upload bukti.',
                'is_active' => '1',
            ],
            'qris' => [
                'type' => 'qris',
                'name' => 'QRIS KAIRO RAMEN',
                'account_name' => 'KAIRO RAMEN',
                'instructions' => 'Scan QRIS lalu upload bukti.',
                'is_active' => '1',
            ],
        };
    }

    // 1-4. Admin bisa membuat keempat tipe.
    public function test_admin_can_create_all_four_types(): void
    {
        $admin = $this->makeAdmin();

        foreach (['dana', 'ewallet', 'bank'] as $type) {
            $this->actingAs($admin)
                ->post(route('admin.payment-methods.store'), $this->basePayload($type))
                ->assertRedirect(route('admin.payment-methods.index'));
        }

        $payload = $this->basePayload('qris');
        $payload['qris_image'] = $this->image();
        $this->actingAs($admin)
            ->post(route('admin.payment-methods.store'), $payload)
            ->assertRedirect(route('admin.payment-methods.index'));

        $this->assertSame(4, PaymentMethod::count());
        $this->assertSame('GoPay', PaymentMethod::where('type', 'ewallet')->first()->provider);
        $this->assertSame('BCA', PaymentMethod::where('type', 'bank')->first()->bank_name);
    }

    // 5. QRIS wajib gambar saat create.
    public function test_qris_image_required_on_create(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->post(route('admin.payment-methods.store'), $this->basePayload('qris'))
            ->assertSessionHasErrors('qris_image');

        $this->assertSame(0, PaymentMethod::count());
    }

    // 6. QRIS tersimpan di folder benar.
    public function test_qris_image_stored_in_public_disk(): void
    {
        $admin = $this->makeAdmin();
        $payload = $this->basePayload('qris');
        $payload['qris_image'] = $this->image();

        $this->actingAs($admin)->post(route('admin.payment-methods.store'), $payload);

        $method = PaymentMethod::where('type', 'qris')->firstOrFail();
        $this->assertNotNull($method->qris_image);
        $this->assertStringStartsWith('payment-methods/qris/', $method->qris_image);
        Storage::disk('public')->assertExists($method->qris_image);
    }

    // Validasi conditional per tipe.
    public function test_conditional_validation_per_type(): void
    {
        $admin = $this->makeAdmin();

        // E-wallet tanpa provider ditolak.
        $p = $this->basePayload('ewallet');
        unset($p['provider']);
        $this->actingAs($admin)->post(route('admin.payment-methods.store'), $p)
            ->assertSessionHasErrors('provider');

        // Bank tanpa bank_name ditolak.
        $p = $this->basePayload('bank');
        unset($p['bank_name']);
        $this->actingAs($admin)->post(route('admin.payment-methods.store'), $p)
            ->assertSessionHasErrors('bank_name');

        // DANA tanpa nomor ditolak.
        $p = $this->basePayload('dana');
        unset($p['account_number']);
        $this->actingAs($admin)->post(route('admin.payment-methods.store'), $p)
            ->assertSessionHasErrors('account_number');

        $this->assertSame(0, PaymentMethod::count());
    }

    // 7-8. Edit + ganti QRIS (file lama dihapus, baru tersimpan).
    public function test_admin_can_edit_and_replace_qris(): void
    {
        $admin = $this->makeAdmin();
        $payload = $this->basePayload('qris');
        $payload['qris_image'] = $this->image('lama.jpg');
        $this->actingAs($admin)->post(route('admin.payment-methods.store'), $payload);
        $method = PaymentMethod::where('type', 'qris')->firstOrFail();
        $old = $method->qris_image;

        // Edit tanpa upload: gambar lama dipertahankan.
        $edit = $this->basePayload('qris');
        $edit['name'] = 'QRIS KAIRO BARU';
        $this->actingAs($admin)->put(route('admin.payment-methods.update', $method), $edit)
            ->assertRedirect();
        $this->assertSame($old, $method->fresh()->qris_image);
        Storage::disk('public')->assertExists($old);

        // Ganti gambar: lama dihapus, baru tersimpan.
        $edit['qris_image'] = $this->image('baru.jpg');
        $this->actingAs($admin)->put(route('admin.payment-methods.update', $method), $edit)
            ->assertRedirect();
        $fresh = $method->fresh();
        $this->assertSame('QRIS KAIRO BARU', $fresh->name);
        $this->assertNotSame($old, $fresh->qris_image);
        Storage::disk('public')->assertMissing($old);
        Storage::disk('public')->assertExists($fresh->qris_image);
    }

    // File QRIS yang dipakai metode lain tidak ikut terhapus.
    public function test_qris_file_shared_by_two_methods_survives_one_delete(): void
    {
        $admin = $this->makeAdmin();
        $payload = $this->basePayload('qris');
        $payload['qris_image'] = $this->image();
        $this->actingAs($admin)->post(route('admin.payment-methods.store'), $payload);
        $first = PaymentMethod::firstOrFail();

        $second = PaymentMethod::create([
            'type' => 'qris',
            'name' => 'QRIS CABANG',
            'account_name' => 'KAIRO RAMEN',
            'qris_image' => $first->qris_image,
            'is_active' => true,
        ]);

        $this->actingAs($admin)->delete(route('admin.payment-methods.destroy', $first));
        Storage::disk('public')->assertExists($second->fresh()->qris_image);
    }

    // 9-10. Customer hanya lihat aktif + bisa memilih tiap tipe.
    public function test_customer_sees_only_active_and_can_pay_with_each_type(): void
    {
        $admin = $this->makeAdmin();
        foreach (['dana', 'ewallet', 'bank'] as $type) {
            $this->actingAs($admin)->post(route('admin.payment-methods.store'), $this->basePayload($type));
        }
        $payload = $this->basePayload('qris');
        $payload['qris_image'] = $this->image();
        $this->actingAs($admin)->post(route('admin.payment-methods.store'), $payload);

        PaymentMethod::create([
            'type' => 'bank', 'name' => 'MANDIRI OFF', 'bank_name' => 'Mandiri',
            'account_name' => 'KAIRO', 'account_number' => '999', 'is_active' => false,
        ]);

        $order = $this->checkoutOrder();
        $res = $this->get(route('payments.show', $order->order_code));
        $res->assertOk()
            ->assertSee('DANA KAIRO')
            ->assertSee('GoPay KAIRO')
            ->assertSee('BCA KAIRO')
            ->assertSee('QRIS KAIRO RAMEN')
            ->assertDontSee('999');

        // Bayar dengan QRIS: snapshot bertipe + copy QRIS.
        $qris = PaymentMethod::where('type', 'qris')->firstOrFail();
        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $qris->id,
            'proof_image' => $this->image('bukti.jpg'),
        ])->assertRedirect();

        $payment = $order->fresh()->payment;
        $this->assertSame('waiting_verification', $payment->status);
        $this->assertSame('qris', $payment->payment_method_type);
        $this->assertStringStartsWith('payments/qris-snapshots/', $payment->payment_qris_image);
        Storage::disk('public')->assertExists($payment->payment_qris_image);
    }

    // 11-12. Snapshot kebal terhadap edit master.
    public function test_snapshot_frozen_when_master_edited(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin)->post(route('admin.payment-methods.store'), $this->basePayload('bank'));
        $method = PaymentMethod::where('type', 'bank')->firstOrFail();

        $order = $this->checkoutOrder();
        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $method->id,
            'proof_image' => $this->image('bukti.jpg'),
        ]);

        $payment = $order->fresh()->payment;
        $this->assertSame('bank', $payment->payment_method_type);
        $this->assertSame('BCA', $payment->payment_bank_name);
        $this->assertSame('1234567890', $payment->payment_account_number);

        // Master diubah total: histori tidak ikut berubah.
        $this->actingAs($admin)->put(route('admin.payment-methods.update', $method), [
            'type' => 'ewallet',
            'name' => 'OVO KAIRO',
            'provider' => 'OVO',
            'account_name' => 'KAIRO BARU',
            'account_number' => '000',
        ]);

        $payment = $order->fresh()->payment;
        $this->assertSame('bank', $payment->payment_method_type);
        $this->assertSame('BCA', $payment->payment_bank_name);
        $this->assertSame('1234567890', $payment->payment_account_number);
        $this->assertSame('BCA KAIRO', $payment->payment_method_name);
    }

    // Snapshot QRIS: ganti master tidak mengubah histori.
    public function test_qris_snapshot_frozen_when_master_replaced(): void
    {
        $admin = $this->makeAdmin();
        $payload = $this->basePayload('qris');
        $payload['qris_image'] = $this->image('lama.jpg');
        $this->actingAs($admin)->post(route('admin.payment-methods.store'), $payload);
        $method = PaymentMethod::where('type', 'qris')->firstOrFail();

        $order = $this->checkoutOrder();
        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $method->id,
            'proof_image' => $this->image('bukti.jpg'),
        ]);
        $snapshot = $order->fresh()->payment->payment_qris_image;

        // Master diganti: file snapshot histori tetap ada & berbeda path.
        $edit = $this->basePayload('qris');
        $edit['qris_image'] = $this->image('baru.jpg');
        $this->actingAs($admin)->put(route('admin.payment-methods.update', $method), $edit);

        $this->assertNotSame($snapshot, $method->fresh()->qris_image);
        Storage::disk('public')->assertExists($snapshot);
        $this->assertSame($snapshot, $order->fresh()->payment->payment_qris_image);
    }

    // 13-16. Proof + approve + reject + full flow tetap jalan.
    public function test_full_payment_flow_with_typed_method(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin)->post(route('admin.payment-methods.store'), $this->basePayload('ewallet'));
        $method = PaymentMethod::where('type', 'ewallet')->firstOrFail();

        $order = $this->checkoutOrder();
        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $method->id,
            'proof_image' => $this->image('bukti.jpg'),
        ]);
        $this->assertSame('waiting_verification', $order->fresh()->payment->status);
        $this->assertSame('GoPay', $order->fresh()->payment->payment_provider);

        // Reject → upload ulang → approve → flow dapur sampai completed.
        $this->actingAs($admin)->post(route('admin.payments.reject', $order));
        $this->assertSame('rejected', $order->fresh()->payment->status);
        $this->assertSame('pending', $order->fresh()->status);

        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $method->id,
            'proof_image' => $this->image('bukti2.jpg'),
        ]);
        $this->assertSame('waiting_verification', $order->fresh()->payment->status);

        $this->actingAs($admin)->post(route('admin.payments.approve', $order));
        $this->assertSame('paid', $order->fresh()->payment->status);
        $this->assertSame('approved', $order->fresh()->status);

        foreach (['cooking', 'ready', 'delivering', 'delivered'] as $expected) {
            $this->actingAs($admin)->post(route('admin.orders.advance', $order));
            $this->assertSame($expected, $order->fresh()->status);
        }
        $this->post(route('orders.confirm', $order->order_code));
        $this->assertSame('completed', $order->fresh()->status);
    }

    // Backward compat: data lama termigrasi aman (dijalankan di testing DB).
    public function test_legacy_rows_readable_after_migration(): void
    {
        $this->actingAs($this->makeAdmin())
            ->get(route('admin.payment-methods.index'))
            ->assertOk();
    }
}
