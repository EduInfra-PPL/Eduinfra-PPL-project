/**
 * Validator sisi klien yang dipakai ulang oleh form Alpine. Aturan harus sama dengan FormRequest di server.
 * Setiap fungsi mengembalikan pesan galat (string) atau null bila valid.
 */
export const validators = {
    npsn: (value) => (/^\d{8}$/.test(String(value ?? '').trim()) ? null : 'NPSN harus 8 digit angka.'),

    required: (label) => (value) => (String(value ?? '').trim() === '' ? `${label} wajib diisi.` : null),

    latitude: (value) => {
        const n = Number(value);
        if (value === '' || value === null || Number.isNaN(n)) return 'Koordinat lintang wajib diisi.';

        return n >= -11.5 && n <= 6.5 ? null : 'Lintang harus berada di wilayah Indonesia.';
    },

    longitude: (value) => {
        const n = Number(value);
        if (value === '' || value === null || Number.isNaN(n)) return 'Koordinat bujur wajib diisi.';

        return n >= 94.5 && n <= 141.5 ? null : 'Bujur harus berada di wilayah Indonesia.';
    },

    email: (value) => {
        const text = String(value ?? '').trim();

        return text === '' || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(text) ? null : 'Format email tidak valid.';
    },
};

/**
 * Menjalankan kumpulan aturan dan mengembalikan objek galat { field: pesan }.
 *
 * @param {Record<string, object>} values  nilai form
 * @param {Record<string, Function>} rules  aturan per field
 * @returns {Record<string, string>}
 */
export function validateFields(values, rules) {
    const errors = {};
    for (const [field, rule] of Object.entries(rules)) {
        const message = rule(values[field]);
        if (message) errors[field] = message;
    }

    return errors;
}
