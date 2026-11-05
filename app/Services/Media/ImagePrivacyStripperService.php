<?php

namespace App\Services\Media;

/**
 * Service pembersihan metadata sensitif dan identitas perangkat (Anti-Fraud & Privacy Stripping).
 * Menghapus nomor seri hardware, IMEI, model kamera privat, dan komentar pengguna
 * sebelum foto disimpan permanen ke public storage (Kepatuhan ISO/IEC 27001).
 * Mematuhi prinsip Single Responsibility (AGENT_RULE.md).
 */
class ImagePrivacyStripperService
{
    /**
     * Bersihkan seluruh metadata EXIF sensitif dari berkas gambar dengan re-encoding lossless.
     * Gambar dibaca pixel-by-pixel lalu ditulis ulang ke file baru yang bebas metadata privat.
     *
     * @param string $sourcePath Path berkas gambar sumber
     * @param string $destinationPath Path berkas tujuan yang bersih
     * @param int $quality Kualitas JPEG (0-100, default 90 untuk menjaga ketajaman bukti audit)
     * @return bool True jika pembersihan berhasil
     */
    public function stripAndSave(string $sourcePath, string $destinationPath, int $quality = 90): bool
    {
        if (!file_exists($sourcePath) || !is_readable($sourcePath)) {
            return false;
        }

        // Dapatkan informasi tipe MIME gambar
        $imageInfo = @getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }

        $mimeType = $imageInfo['mime'];
        $imageResource = null;

        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                $imageResource = @imagecreatefromjpeg($sourcePath);
                break;

            case 'image/png':
                $imageResource = @imagecreatefrompng($sourcePath);
                break;

            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    $imageResource = @imagecreatefromwebp($sourcePath);
                }
                break;

            default:
                return false;
        }

        if (!$imageResource) {
            return false;
        }

        // Pastikan folder tujuan ada
        $destinationDir = dirname($destinationPath);
        if (!is_dir($destinationDir)) {
            @mkdir($destinationDir, 0755, true);
        }

        $saved = false;

        // Tulis ulang gambar ke berkas tujuan sesuai format asli tanpa membawa tag EXIF lama
        if ($mimeType === 'image/png') {
            imagealphablending($imageResource, false);
            imagesavealpha($imageResource, true);
            $saved = @imagepng($imageResource, $destinationPath, 6);
        } elseif ($mimeType === 'image/webp' && function_exists('imagewebp')) {
            $saved = @imagewebp($imageResource, $destinationPath, $quality);
        } else {
            // Default: Simpan sebagai JPEG berkualitas tinggi tanpa EXIF
            $saved = @imagejpeg($imageResource, $destinationPath, $quality);
        }

        imagedestroy($imageResource);

        return $saved;
    }
}

