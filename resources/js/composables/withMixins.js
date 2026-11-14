/**
 * Menggabungkan state dasar dengan satu atau lebih mixin Alpine tanpa memanggil getter.
 *
 * Object spread (`{ ...mixin }`) mengevaluasi getter sekali dan menyimpan nilainya,
 * sehingga reaktivitas Alpine hilang. Karena itu deskriptor properti disalin apa adanya.
 *
 * @param {object} base  state dan getter milik komponen
 * @param {...object} mixins  objek berisi getter/method yang dipakai ulang
 * @returns {object}  objek baru yang siap dikembalikan ke Alpine.data()
 */
export function withMixins(base, ...mixins) {
    const descriptors = Object.getOwnPropertyDescriptors(base);

    for (const mixin of mixins) {
        Object.assign(descriptors, Object.getOwnPropertyDescriptors(mixin));
    }

    return Object.defineProperties({}, descriptors);
}
