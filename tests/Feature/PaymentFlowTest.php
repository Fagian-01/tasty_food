<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
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
        Storage::fake('public');
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

    private function makeMethod(array $over = []): PaymentMethod
    {
        return PaymentMethod::create(array_merge([
            'type' => 'dana',
            'name' => 'DANA',
            'account_name' => 'KAIRO RAMEN',
            'account_number' => '081234567890',
            'instructions' => 'Transfer lalu upload bukti.',
            'is_active' => true,
        ], $over));
    }

    private function checkoutOrder(int $qty = 1): Order
    {
        $menu = $this->makeMenu();
        $this->withSession(['cart' => [$menu->id => $qty]])
            ->post('/checkout', [
                'customer_name' => 'Budi',
                'customer_phone' => '08123456789',
                'customer_address' => 'Jl Asia Afrika 1',
            ]);

        return Order::firstOrFail();
    }

    private function proof(): UploadedFile
    {
        // fake()->image() butuh ekstensi GD; fake()->create() cukup karena
        // validasi `image` Laravel memeriksa MIME, bukan memproses gambar.
        return UploadedFile::fake()->create('bukti.jpg', 500, 'image/jpeg');
    }

    public function test_checkout_creates_unpaid_payment(): void
    {
        $this->makeMethod();
        $order = $this->checkoutOrder(2);

        $payment = $order->fresh()->payment;
        $this->assertNotNull($payment);
        $this->assertSame('unpaid', $payment->status);
        $this->assertSame($order->total_amount, $payment->amount);
        $this->assertSame(76000, (int) $payment->amount);
    }

    public function test_proof_required_before_submit(): void
    {
        $method = $this->makeMethod();
        $order = $this->checkoutOrder();

        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $method->id,
        ])->assertSessionHasErrors('proof_image');

        $this->assertSame('unpaid', $order->fresh()->payment->status);
    }

    public function test_inactive_method_rejected(): void
    {
        $off = $this->makeMethod(['name' => 'MANDIRI', 'account_number' => '999', 'is_active' => false]);
        $order = $this->checkoutOrder();

        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $off->id,
            'proof_image' => $this->proof(),
        ])->assertSessionHasErrors('payment_method');

        $this->assertSame('unpaid', $order->fresh()->payment->status);
    }

    public function test_valid_proof_becomes_waiting_verification(): void
    {
        $method = $this->makeMethod();
        $order = $this->checkoutOrder();

        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $method->id,
            'proof_image' => $this->proof(),
        ])->assertRedirect(route('payments.show', $order->order_code));

        $payment = $order->fresh()->payment;
        $this->assertSame('waiting_verification', $payment->status);
        $this->assertNotNull($payment->proof_image);
        Storage::disk('public')->assertExists($payment->proof_image);
        // Snapshot tersimpan.
        $this->assertSame('DANA', $payment->payment_method_name);
        $this->assertSame('dana', $payment->payment_method_type);
        $this->assertSame('KAIRO RAMEN', $payment->payment_account_name);
        $this->assertSame('081234567890', $payment->payment_account_number);
        // Nominal dari server, bukan input.
        $this->assertSame($order->total_amount, $payment->amount);
        // Order belum masuk cooking.
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_customer_cannot_force_paid_status(): void
    {
        $method = $this->makeMethod();
        $order = $this->checkoutOrder();

        // Coba selipkan status=paid + amount palsu via request manual.
        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $method->id,
            'proof_image' => $this->proof(),
            'status' => 'paid',
            'amount' => 100,
        ])->assertRedirect();

        $payment = $order->fresh()->payment;
        $this->assertSame('waiting_verification', $payment->status);
        $this->assertSame($order->total_amount, $payment->amount);
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_non_image_proof_rejected(): void
    {
        $method = $this->makeMethod();
        $order = $this->checkoutOrder();

        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $method->id,
            'proof_image' => UploadedFile::fake()->create('jahat.php', 100, 'application/x-php'),
        ])->assertSessionHasErrors('proof_image');

        $this->assertSame('unpaid', $order->fresh()->payment->status);
    }

    public function test_cannot_replace_proof_while_waiting_or_paid(): void
    {
        $method = $this->makeMethod();
        $order = $this->checkoutOrder();
        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $method->id,
            'proof_image' => $this->proof(),
        ]);
        $first = $order->fresh()->payment->proof_image;

        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $method->id,
            'proof_image' => $this->proof(),
        ])->assertSessionHasErrors('payment');

        $this->assertSame($first, $order->fresh()->payment->proof_image);
    }

    public function test_admin_can_view_proof_and_approve_atomically(): void
    {
        $admin = $this->makeAdmin();
        $method = $this->makeMethod();
        $order = $this->checkoutOrder();
        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $method->id,
            'proof_image' => $this->proof(),
        ]);

        // Admin melihat bukti transfer di detail order.
        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Bukti Transfer')
            ->assertSee('APPROVE PESANAN');

        // Approve: payment=paid + order=approved sekaligus.
        $this->actingAs($admin)
            ->post(route('admin.payments.approve', $order))
            ->assertSessionHasNoErrors();

        $this->assertSame('paid', $order->fresh()->payment->status);
        $this->assertNotNull($order->fresh()->payment->paid_at);
        $this->assertSame('approved', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->approved_at);

        // Idempotent: klik dua kali tidak merusak status.
        $this->actingAs($admin)->post(route('admin.payments.approve', $order));
        $this->assertSame('paid', $order->fresh()->payment->status);
        $this->assertSame('approved', $order->fresh()->status);
    }

    public function test_admin_reject_keeps_order_out_of_kitchen(): void
    {
        $admin = $this->makeAdmin();
        $method = $this->makeMethod();
        $order = $this->checkoutOrder();
        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $method->id,
            'proof_image' => $this->proof(),
        ]);

        $this->actingAs($admin)->post(route('admin.payments.reject', $order));

        $this->assertSame('rejected', $order->fresh()->payment->status);
        // Order TIDAK berubah: tidak approved, tidak completed, tidak rejected.
        $this->assertSame('pending', $order->fresh()->status);

        // Advance ke cooking harus ditolak selama payment rejected.
        $this->actingAs($admin)->post(route('admin.orders.approve', $order));
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_customer_can_resubmit_after_reject(): void
    {
        $admin = $this->makeAdmin();
        $method = $this->makeMethod();
        $order = $this->checkoutOrder();
        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $method->id,
            'proof_image' => $this->proof(),
        ]);
        $this->actingAs($admin)->post(route('admin.payments.reject', $order));

        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $method->id,
            'proof_image' => $this->proof(),
        ])->assertRedirect();

        $payment = $order->fresh()->payment;
        $this->assertSame('waiting_verification', $payment->status);
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_payment_method_crud(): void
    {
        $admin = $this->makeAdmin();

        // Create
        $this->actingAs($admin)->post(route('admin.payment-methods.store'), [
            'type' => 'bank',
            'name' => 'BCA',
            'bank_name' => 'BCA',
            'account_name' => 'KAIRO RAMEN',
            'account_number' => '1234567890',
            'is_active' => '1',
        ])->assertRedirect(route('admin.payment-methods.index'));
        $method = PaymentMethod::where('name', 'BCA')->firstOrFail();
        $this->assertSame('bank', $method->type);

        // Update + nonaktifkan
        $this->actingAs($admin)->put(route('admin.payment-methods.update', $method), [
            'type' => 'bank',
            'name' => 'BCA',
            'bank_name' => 'BCA',
            'account_name' => 'KAIRO RAMEN',
            'account_number' => '111222333',
        ])->assertRedirect();
        $this->assertSame('111222333', $method->fresh()->account_number);
        $this->assertFalse($method->fresh()->is_active);

        // Delete aman
        $this->actingAs($admin)->delete(route('admin.payment-methods.destroy', $method))
            ->assertRedirect();
        $this->assertNull(PaymentMethod::find($method->id));
    }

    public function test_customer_only_sees_active_methods(): void
    {
        $this->makeMethod(['name' => 'DANA']);
        $this->makeMethod(['name' => 'MANDIRI', 'account_number' => '9876543210', 'is_active' => false]);
        $order = $this->checkoutOrder();

        $res = $this->get(route('payments.show', $order->order_code));
        $res->assertOk()->assertSee('DANA')->assertDontSee('9876543210');
    }

    public function test_snapshot_survives_master_change_and_delete(): void
    {
        $method = $this->makeMethod();
        $order = $this->checkoutOrder();
        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $method->id,
            'proof_image' => $this->proof(),
        ]);

        // Admin ubah nomor master: histori order tidak ikut berubah.
        $method->update(['account_number' => '0000000000']);
        $payment = $order->fresh()->payment;
        $this->assertSame('081234567890', $payment->payment_account_number);

        // Admin hapus master: snapshot tetap utuh (nullOnDelete).
        $method->delete();
        $payment = $order->fresh()->payment;
        $this->assertNull($payment->payment_method_id);
        $this->assertSame('DANA', $payment->payment_method_name);
        $this->assertSame('081234567890', $payment->payment_account_number);
    }

    public function test_full_flow_to_completed_after_payment_approved(): void
    {
        $admin = $this->makeAdmin();
        $method = $this->makeMethod();
        $order = $this->checkoutOrder();
        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $method->id,
            'proof_image' => $this->proof(),
        ]);
        $this->actingAs($admin)->post(route('admin.payments.approve', $order));

        foreach (['cooking', 'ready', 'delivering', 'delivered'] as $expected) {
            $this->actingAs($admin)->post(route('admin.orders.advance', $order));
            $this->assertSame($expected, $order->fresh()->status);
        }

        $this->post(route('orders.confirm', $order->order_code));
        $this->assertSame('completed', $order->fresh()->status);
        $this->assertSame('paid', $order->fresh()->payment->status);
    }

    public function test_tracking_shows_payment_status(): void
    {
        // Flow baru: tracking TERKUNCI sebelum approve — customer melihat
        // status via halaman payment, bukan halaman tracking.
        $admin = $this->makeAdmin();
        $method = $this->makeMethod();
        $order = $this->checkoutOrder();

        // Belum bayar: tracking dialihkan ke payment.
        $this->get(route('orders.show', $order->order_code))
            ->assertRedirect(route('payments.show', $order->order_code));
        $this->get(route('payments.show', $order->order_code))
            ->assertOk()->assertSee('Belum Bayar');

        // Sudah upload: masih dikunci, payment tampil menunggu verifikasi.
        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $method->id,
            'proof_image' => $this->proof(),
        ]);
        $this->get(route('orders.show', $order->order_code))
            ->assertRedirect(route('payments.show', $order->order_code));
        $this->get(route('payments.show', $order->order_code))
            ->assertOk()->assertSee('Menunggu Verifikasi');

        // Ditolak: payment tampil info tolak + form upload ulang.
        $this->actingAs($admin)->post(route('admin.payments.reject', $order));
        $this->get(route('payments.show', $order->order_code))
            ->assertOk()->assertSee('Pembayaran ditolak');

        // Approve: tracking terbuka + tampil lunas.
        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $method->id,
            'proof_image' => $this->proof(),
        ]);
        $this->actingAs($admin)->post(route('admin.payments.approve', $order));
        $this->get(route('orders.show', $order->order_code))
            ->assertOk()->assertSee('berhasil diverifikasi');
    }

    public function test_orders_without_payment_cannot_enter_kitchen(): void
    {
        // Flow baru: tidak ada jalan ke cooking tanpa payment paid —
        // termasuk order tanpa baris payment (data lama / nakal).
        $admin = $this->makeAdmin();
        $menu = $this->makeMenu();
        $order = Order::create([
            'order_code' => 'KAIRO-8001',
            'customer_name' => 'Lawas',
            'customer_phone' => '08111',
            'customer_address' => 'Jl Test',
            'total_amount' => $menu->price,
            'status' => 'pending',
        ]);

        // Approve terpisah tidak berlaku.
        $this->actingAs($admin)->post(route('admin.orders.approve', $order));
        $this->assertSame('pending', $order->fresh()->status);

        // Paksa approved pun tetap tidak bisa ke cooking tanpa paid.
        $order->update(['status' => 'approved', 'approved_at' => now()]);
        $this->actingAs($admin)->post(route('admin.orders.advance', $order));
        $this->assertSame('approved', $order->fresh()->status);
    }

    public function test_admin_payment_routes_require_auth(): void
    {
        $order = $this->checkoutOrder();

        $this->post(route('admin.payments.approve', $order))->assertRedirect();
        $this->post(route('admin.payments.reject', $order))->assertRedirect();
        $this->get(route('admin.payment-methods.index'))->assertRedirect();
    }

    public function test_customer_cannot_hit_admin_payment_routes(): void
    {
        // Tanpa login admin, customer tidak bisa approve/reject walau tahu URL.
        $method = $this->makeMethod();
        $order = $this->checkoutOrder();
        $this->post(route('payments.store', $order->order_code), [
            'payment_method' => $method->id,
            'proof_image' => $this->proof(),
        ]);

        $this->post(route('admin.payments.approve', $order))->assertRedirect();
        $this->assertSame('waiting_verification', $order->fresh()->payment->status);
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_payment_record_is_one_per_order(): void
    {
        $this->makeMethod();
        $order = $this->checkoutOrder();

        $this->assertSame(1, Payment::where('order_id', $order->id)->count());
    }
}
