/**
 * Klasifikasi nomor identitas: NIP (18 digit), NIK (16 digit), kosong, atau tidak valid.
 *
 * @param {string} value
 * @returns {'empty' | 'nip' | 'nik' | 'invalid'}
 */
export function identityStateOf(value) {
    const digits = (value || '').replace(/\D/g, '');

    if (digits.length === 0) return 'empty';
    if (digits.length === 18) return 'nip';
    if (digits.length === 16) return 'nik';

    return 'invalid';
}

/**
 * Mixin Alpine: getter `identityState` yang membaca properti state `key`.
 *
 * @param {string} [key='identity']
 */
export function identityNumberMixin(key = 'identity') {
    return {
        get identityState() {
            return identityStateOf(this[key]);
        },
    };
}
