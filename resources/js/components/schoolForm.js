import { validateFields, validators } from '../composables/useFieldValidation';

/**
 * Form tambah sekolah (Alpine.data 'schoolForm'). Validasi klien mencegah pengiriman
 * yang pasti ditolak server; server tetap memvalidasi ulang.
 *
 * @param {{ npsn?: string, name?: string, educational_level?: string, address?: string, lat?: string, lng?: string, accreditation?: string, contact_phone?: string, contact_email?: string }} [initial]
 */
export function schoolForm(initial = {}) {
    return {
        values: {
            npsn: initial.npsn ?? '',
            name: initial.name ?? '',
            educational_level: initial.educational_level ?? '',
            address: initial.address ?? '',
            lat: initial.lat ?? '',
            lng: initial.lng ?? '',
            accreditation: initial.accreditation ?? '',
            contact_phone: initial.contact_phone ?? '',
            contact_email: initial.contact_email ?? '',
        },
        errors: {},

        rules() {
            return {
                npsn: validators.npsn,
                name: validators.required('Nama sekolah'),
                educational_level: validators.required('Jenjang'),
                lat: validators.latitude,
                lng: validators.longitude,
                contact_email: validators.email,
            };
        },

        /** Dipanggil pada event submit; membatalkan pengiriman bila ada galat. */
        validate(event) {
            this.errors = validateFields(this.values, this.rules());

            if (Object.keys(this.errors).length > 0) {
                event.preventDefault();

                return false;
            }

            return true;
        },

        hasError(field) {
            return Boolean(this.errors[field]);
        },
    };
}
