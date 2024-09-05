<?php



namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;


class QrCodeService
{
    public function generateQrCode($data)
    {
        // Génère un QR code avec les données fournies
        return QrCode::format('png')->size(300)->generate($data);
    }
}
