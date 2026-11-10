<?php

namespace App\Actions\Schools;

use App\Models\School;

/**
 * Mengubah kontak dan akreditasi sekolah (Tabel 16: U = perubahan kontak/akreditasi).
 * Nama, NPSN, dan koordinat tidak diubah di sini; koreksinya lewat impor.
 */
class UpdateSchoolContactAction
{
    /** @return bool true bila sekolah ditemukan dan diperbarui */
    public function execute(int $id, array $data): bool
    {
        // hanya kolom yang benar-benar dikirim yang diubah; kolom lain dibiarkan
        $changes = array_intersect_key($data, array_flip(['accreditation', 'contact_phone', 'contact_email']));
        if ($changes === []) {
            return false;
        }

        return School::query()->active()->where('id', $id)->update($changes) > 0;
    }
}
