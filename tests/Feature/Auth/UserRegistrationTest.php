<?php

namespace Tests\Feature\Auth;

use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Registrasi akun (Domain 5).
 *
 * Berjalan pada database PostgreSQL tim (docker) yang sudah dimigrasi. Memakai DatabaseTransactions,
 * bukan RefreshDatabase, supaya data seed (regions, schools) tidak terhapus: semua data uji dibatalkan otomatis.
 */
class UserRegistrationTest extends TestCase
{
    use DatabaseTransactions;

    private const EMAIL = 'pendaftar.uji@kemdikbud.go.id';

    private const NIP = '199001012015011009';

    private const PASSWORD = 'KemenDikbud#3T2024!';

    private function createRegion(array $overrides = []): Region
    {
        return Region::query()->create(array_merge([
            'code' => 'TS.01',
            'name' => 'Kabupaten Uji Registrasi',
            'level' => 'kab',
            'is_3t_tier' => 1,
        ], $overrides));
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(Region $region, array $overrides = []): array
    {
        return array_merge([
            'registration_role' => 'verifikator_3t',
            'name' => 'Pendaftar Uji Registrasi',
            'identity_number' => self::NIP,
            'email' => self::EMAIL,
            'phone' => '+62 812-4498-2100',
            'region_id' => $region->id,
            'organization' => 'Balai Prasarana Permukiman Wilayah',
            'password' => self::PASSWORD,
            'password_confirmation' => self::PASSWORD,
            'terms' => '1',
        ], $overrides);
    }

    public function test_registration_page_lists_3t_regions_only(): void
    {
        $this->createRegion();
        $this->createRegion(['code' => 'TS.02', 'name' => 'Kabupaten Non 3T Uji', 'is_3t_tier' => 0]);

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Formulir Registrasi Pengguna Baru')
            ->assertSee('Kabupaten Uji Registrasi')
            ->assertDontSee('Kabupaten Non 3T Uji');
    }

    public function test_new_user_is_registered_unverified_and_not_logged_in(): void
    {
        $region = $this->createRegion();

        $this->post(route('register.store'), $this->payload($region))
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $this->assertGuest();

        $user = User::query()->where('email', self::EMAIL)->firstOrFail();

        $this->assertFalse((bool) $user->is_verified);
        $this->assertSame(self::NIP, $user->identity_number);
        $this->assertSame('+6281244982100', $user->phone);
        $this->assertSame('Balai Prasarana Permukiman Wilayah', $user->organization);
        $this->assertSame($region->id, (int) $user->region_id);
        $this->assertSame('verifikator_3t', $user->registration_role);
        $this->assertSame('auditor_relawan', $user->role_enum);
        $this->assertNotNull($user->terms_accepted_at);
        $this->assertTrue(Hash::check(self::PASSWORD, $user->password));
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function roleMappingProvider(): array
    {
        return [
            'satdik sekolah 3t -> pelapor_guru' => ['satdik_sekolah_3t', 'pelapor_guru'],
            'verifikator 3t -> auditor_relawan' => ['verifikator_3t', 'auditor_relawan'],
            'auditor pengawas -> auditor_relawan' => ['auditor_pengawas', 'auditor_relawan'],
            'konsultan rekanan -> mitra_csr' => ['konsultan_rekanan', 'mitra_csr'],
        ];
    }

    #[DataProvider('roleMappingProvider')]
    public function test_registration_role_is_mapped_to_rbac_persona(string $card, string $persona): void
    {
        $region = $this->createRegion();

        $this->post(route('register.store'), $this->payload($region, ['registration_role' => $card]))
            ->assertRedirect(route('login'));

        $user = User::query()->where('email', self::EMAIL)->firstOrFail();

        $this->assertSame($card, $user->registration_role);
        $this->assertSame($persona, $user->role_enum);
    }

    public function test_nik_with_16_digits_is_accepted(): void
    {
        $region = $this->createRegion();

        $this->post(route('register.store'), $this->payload($region, ['identity_number' => '3174010101900001']))
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', ['email' => self::EMAIL, 'identity_number' => '3174010101900001']);
    }

    public function test_identity_number_with_spaces_is_normalized(): void
    {
        $region = $this->createRegion();

        $this->post(route('register.store'), $this->payload($region, ['identity_number' => '19900101 201501 1 009']))
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', ['email' => self::EMAIL, 'identity_number' => self::NIP]);
    }

    public function test_identity_number_must_be_16_or_18_digits(): void
    {
        $region = $this->createRegion();

        $this->post(route('register.store'), $this->payload($region, ['identity_number' => '12345']))
            ->assertSessionHasErrors('identity_number');

        $this->assertDatabaseMissing('users', ['email' => self::EMAIL]);
    }

    public function test_duplicate_email_is_rejected(): void
    {
        $region = $this->createRegion();
        User::factory()->create(['email' => self::EMAIL]);

        $this->post(route('register.store'), $this->payload($region))
            ->assertSessionHasErrors('email');
    }

    public function test_duplicate_identity_number_is_rejected(): void
    {
        $region = $this->createRegion();
        User::factory()->create(['identity_number' => self::NIP]);

        $this->post(route('register.store'), $this->payload($region))
            ->assertSessionHasErrors('identity_number');
    }

    public function test_invalid_phone_number_is_rejected(): void
    {
        $region = $this->createRegion();

        $this->post(route('register.store'), $this->payload($region, ['phone' => '12345']))
            ->assertSessionHasErrors('phone');
    }

    public function test_password_confirmation_must_match(): void
    {
        $region = $this->createRegion();

        $this->post(route('register.store'), $this->payload($region, ['password_confirmation' => 'berbeda']))
            ->assertSessionHasErrors('password');
    }

    public function test_pakta_integritas_must_be_accepted(): void
    {
        $region = $this->createRegion();

        $this->post(route('register.store'), $this->payload($region, ['terms' => null]))
            ->assertSessionHasErrors('terms');
    }

    public function test_region_must_be_a_3t_kabupaten(): void
    {
        $nonThreeT = $this->createRegion(['code' => 'TS.02', 'is_3t_tier' => 0]);
        $province = $this->createRegion(['code' => 'TS', 'name' => 'Provinsi Uji', 'level' => 'prov', 'is_3t_tier' => 1]);

        $this->post(route('register.store'), $this->payload($nonThreeT))
            ->assertSessionHasErrors('region_id');

        $this->post(route('register.store'), $this->payload($province))
            ->assertSessionHasErrors('region_id');
    }

    public function test_admin_role_cannot_be_self_registered(): void
    {
        $region = $this->createRegion();

        $this->post(route('register.store'), $this->payload($region, ['registration_role' => 'admin_dinas']))
            ->assertSessionHasErrors('registration_role');

        $this->assertDatabaseMissing('users', ['email' => self::EMAIL]);
    }

    public function test_registrant_cannot_login_until_verified(): void
    {
        $region = $this->createRegion();

        $this->post(route('register.store'), $this->payload($region));

        $this->post(route('login.store'), ['email' => self::EMAIL, 'password' => self::PASSWORD])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        User::query()->where('email', self::EMAIL)->update(['is_verified' => true]);

        $this->post(route('login.store'), ['email' => self::EMAIL, 'password' => self::PASSWORD]);
        $this->assertAuthenticated();
    }

    public function test_authenticated_user_cannot_open_registration_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('register'))
            ->assertRedirect();
    }
}