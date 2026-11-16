import { identityNumberMixin } from '../composables/useIdentityNumber';
import { passwordConfirmationMixin, passwordStrengthMixin } from '../composables/usePasswordStrength';
import { withMixins } from '../composables/withMixins';

/**
 * State form registrasi (Alpine.data). Logika identitas dan kata sandi berasal dari composable
 * yang sama dengan komponen lain, sehingga tidak perlu ditulis ulang di tiap halaman.
 *
 * @param {{ role?: string, identity?: string }} [initial]
 */
export function registrationForm(initial = {}) {
    return withMixins(
        {
            role: initial.role || 'verifikator_3t',
            identity: initial.identity || '',
            password: '',
            confirmation: '',
        },
        identityNumberMixin('identity'),
        passwordStrengthMixin('password'),
        passwordConfirmationMixin({ password: 'password', confirmation: 'confirmation' }),
    );
}
