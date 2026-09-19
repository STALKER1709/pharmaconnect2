<?php

namespace Tests\Unit;

use App\Support\Fcfa;
use PHPUnit\Framework\TestCase;

class FcfaTest extends TestCase
{
    public function test_format_fcfa_formate_les_milliers(): void
    {
        $this->assertSame('12 500 FCFA', format_fcfa(12500));
        $this->assertSame('1 000 000 FCFA', format_fcfa(1000000));
        $this->assertSame('0 FCFA', format_fcfa(0));
        $this->assertSame('0 FCFA', format_fcfa(null));
    }

    public function test_fcfa_montant_arrondit_et_formate(): void
    {
        $this->assertSame('2 500 FCFA', Fcfa::montant(2500));
        $this->assertSame('2 500 FCFA', Fcfa::montant(2500.4));
        $this->assertSame('2 501 FCFA', Fcfa::montant(2500.6));
    }

    public function test_distance_haversine_entre_deux_points_connus(): void
    {
        // Douala (Bonanjo) → Douala (Akwa) ≈ 2,6 km ; Bonanjo → Yaoundé ≈ 180 km
        $doualaBonanjo = [4.0483, 9.7043];
        $doualaAkwa = [4.0487, 9.7340];
        $yaounde = [3.8667, 11.5167];

        $intraDouala = Fcfa::distanceKm($doualaBonanjo[0], $doualaBonanjo[1], $doualaAkwa[0], $doualaAkwa[1]);
        $versYaounde = Fcfa::distanceKm($doualaBonanjo[0], $doualaBonanjo[1], $yaounde[0], $yaounde[1]);

        $this->assertNotNull($intraDouala);
        $this->assertGreaterThan(2.0, $intraDouala);
        $this->assertLessThan(4.0, $intraDouala);
        $this->assertGreaterThan(150.0, $versYaounde);
        $this->assertLessThan(220.0, $versYaounde);
    }

    public function test_distance_haversine_retourne_null_si_coordonnees_manquantes(): void
    {
        $this->assertNull(Fcfa::distanceKm(null, 9.7, 4.05, 9.76));
        $this->assertNull(Fcfa::distanceKm(4.05, null, 4.05, 9.76));
    }

    public function test_haversine_km_zero_distance_meme_point(): void
    {
        $this->assertSame(0.0, haversine_km(4.05, 9.76, 4.05, 9.76));
    }
}
