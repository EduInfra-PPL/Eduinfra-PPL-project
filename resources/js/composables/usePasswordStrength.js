const STRENGTH_LABELS = ['', 'Lemah', 'Cukup', 'Kuat', 'Sangat Kuat'];
const STRENGTH_COLORS = ['bg-slate-200', 'bg-red-500', 'bg-amber-500', 'bg-emerald-400', 'bg-emerald-500'];

/**
 * Skor kekuatan kata sandi 0-4: panjang >= 12, huruf besar+kecil, angka, simbol.
 *
 * @param {string} value
 * @returns {number}
 */
export function passwordScore(value) {
    if (!value) return 0;

    return [
        value.length >= 12,
        /[a-z]/.test(value) && /[A-Z]/.test(value),
        /\d/.test(value),
        /[^A-Za-z0-9]/.test(value),
    ].filter(Boolean).length;
}

/**
 * Mixin Alpine: getter kekuatan kata sandi yang membaca properti state `key`.
 *
 * @param {string} [key='password']
 */
export function passwordStrengthMixin(key = 'password') {
    return {
        get score() {
            return passwordScore(this[key]);
        },

        get strengthLabel() {
            return STRENGTH_LABELS[this.score];
        },

        get strengthPercent() {
            return this.score * 25;
        },

        get strengthColor() {
            return STRENGTH_COLORS[this.score];
        },
    };
}

/**
 * Mixin Alpine: status kecocokan konfirmasi kata sandi ('empty' | 'match' | 'mismatch').
 *
 * @param {{ password?: string, confirmation?: string }} [keys]
 */
export function passwordConfirmationMixin({ password = 'password', confirmation = 'confirmation' } = {}) {
    return {
        get confirmationState() {
            if (!this[confirmation]) return 'empty';

            return this[confirmation] === this[password] ? 'match' : 'mismatch';
        },
    };
}
