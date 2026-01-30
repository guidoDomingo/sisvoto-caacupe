<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Distrito;
use App\Models\Zona;
use App\Models\Barrio;

class DatosMaestrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear distritos
        $asuncion = Distrito::create([
            'nombre' => 'Capital',
            'codigo' => 'CAP',
            'descripcion' => 'Distrito Capital - Asunción'
        ]);

        $central = Distrito::create([
            'nombre' => 'Central',
            'codigo' => 'CEN',
            'descripcion' => 'Departamento Central'
        ]);

        // Crear zonas para Asunción
        $centro = Zona::create([
            'nombre' => 'Centro',
            'codigo' => 'CTR',
            'distrito_id' => $asuncion->id,
            'color' => '#FF6B6B',
            'descripcion' => 'Zona Céntrica de Asunción'
        ]);

        $este = Zona::create([
            'nombre' => 'Este',
            'codigo' => 'EST',
            'distrito_id' => $asuncion->id,
            'color' => '#4ECDC4',
            'descripcion' => 'Zona Este de Asunción'
        ]);

        $oeste = Zona::create([
            'nombre' => 'Oeste',
            'codigo' => 'OES',
            'distrito_id' => $asuncion->id,
            'color' => '#45B7D1',
            'descripcion' => 'Zona Oeste de Asunción'
        ]);

        $norte = Zona::create([
            'nombre' => 'Norte',
            'codigo' => 'NOR',
            'distrito_id' => $asuncion->id,
            'color' => '#96CEB4',
            'descripcion' => 'Zona Norte de Asunción'
        ]);

        $sur = Zona::create([
            'nombre' => 'Sur',
            'codigo' => 'SUR',
            'distrito_id' => $asuncion->id,
            'color' => '#FFEAA7',
            'descripcion' => 'Zona Sur de Asunción'
        ]);

        // Crear barrios para Zona Centro
        Barrio::create([
            'nombre' => 'Casco Histórico',
            'codigo' => 'CH',
            'zona_id' => $centro->id,
            'latitud' => -25.2637,
            'longitud' => -57.5759,
            'descripcion' => 'Centro histórico de Asunción'
        ]);

        Barrio::create([
            'nombre' => 'Catedral',
            'codigo' => 'CAT',
            'zona_id' => $centro->id,
            'latitud' => -25.2828,
            'longitud' => -57.6347,
            'descripcion' => 'Barrio de la Catedral'
        ]);

        // Crear barrios para Zona Este
        Barrio::create([
            'nombre' => 'Villa Morra',
            'codigo' => 'VM',
            'zona_id' => $este->id,
            'latitud' => -25.2924,
            'longitud' => -57.5762,
            'descripcion' => 'Barrio comercial de Villa Morra'
        ]);

        Barrio::create([
            'nombre' => 'Las Carmelitas',
            'codigo' => 'LC',
            'zona_id' => $este->id,
            'latitud' => -25.2901,
            'longitud' => -57.5834,
            'descripcion' => 'Barrio residencial Las Carmelitas'
        ]);

        Barrio::create([
            'nombre' => 'Recoleta',
            'codigo' => 'REC',
            'zona_id' => $este->id,
            'latitud' => -25.2945,
            'longitud' => -57.5698,
            'descripcion' => 'Barrio de Recoleta'
        ]);

        // Crear barrios para Zona Oeste
        Barrio::create([
            'nombre' => 'Sajonia',
            'codigo' => 'SAJ',
            'zona_id' => $oeste->id,
            'latitud' => -25.2742,
            'longitud' => -57.6211,
            'descripcion' => 'Barrio Sajonia'
        ]);

        Barrio::create([
            'nombre' => 'Villa Aurelia',
            'codigo' => 'VA',
            'zona_id' => $oeste->id,
            'latitud' => -25.2801,
            'longitud' => -57.6156,
            'descripcion' => 'Barrio Villa Aurelia'
        ]);

        Barrio::create([
            'nombre' => 'Botánico',
            'codigo' => 'BOT',
            'zona_id' => $oeste->id,
            'latitud' => -25.2678,
            'longitud' => -57.6134,
            'descripcion' => 'Barrio Botánico'
        ]);

        // Crear barrios para Zona Norte
        Barrio::create([
            'nombre' => 'Doctor Francia',
            'codigo' => 'DF',
            'zona_id' => $norte->id,
            'latitud' => -25.2534,
            'longitud' => -57.5823,
            'descripcion' => 'Barrio Dr. Francia'
        ]);

        Barrio::create([
            'nombre' => 'Villa del Rosario',
            'codigo' => 'VR',
            'zona_id' => $norte->id,
            'latitud' => -25.2456,
            'longitud' => -57.5892,
            'descripcion' => 'Barrio Villa del Rosario'
        ]);

        // Crear barrios para Zona Sur
        Barrio::create([
            'nombre' => 'Tablada Nueva',
            'codigo' => 'TN',
            'zona_id' => $sur->id,
            'latitud' => -25.3123,
            'longitud' => -57.5967,
            'descripcion' => 'Barrio Tablada Nueva'
        ]);

        Barrio::create([
            'nombre' => 'Villa Elisa',
            'codigo' => 'VE',
            'zona_id' => $sur->id,
            'latitud' => -25.3234,
            'longitud' => -57.6045,
            'descripcion' => 'Barrio Villa Elisa'
        ]);

        // Crear zonas para Central
        $sanLorenzo = Zona::create([
            'nombre' => 'San Lorenzo',
            'codigo' => 'SL',
            'distrito_id' => $central->id,
            'color' => '#DDA0DD',
            'descripcion' => 'Zona de San Lorenzo'
        ]);

        $fernando = Zona::create([
            'nombre' => 'Fernando de la Mora',
            'codigo' => 'FDM',
            'distrito_id' => $central->id,
            'color' => '#F0E68C',
            'descripcion' => 'Zona de Fernando de la Mora'
        ]);

        // Crear barrios para San Lorenzo
        Barrio::create([
            'nombre' => 'Centro San Lorenzo',
            'codigo' => 'CSL',
            'zona_id' => $sanLorenzo->id,
            'latitud' => -25.3345,
            'longitud' => -57.5067,
            'descripcion' => 'Centro de San Lorenzo'
        ]);

        Barrio::create([
            'nombre' => 'Santa María',
            'codigo' => 'SM',
            'zona_id' => $sanLorenzo->id,
            'latitud' => -25.3423,
            'longitud' => -57.5123,
            'descripcion' => 'Barrio Santa María'
        ]);

        // Crear barrios para Fernando de la Mora
        Barrio::create([
            'nombre' => 'Centro Fernando',
            'codigo' => 'CF',
            'zona_id' => $fernando->id,
            'latitud' => -25.3234,
            'longitud' => -57.5456,
            'descripcion' => 'Centro de Fernando de la Mora'
        ]);

        Barrio::create([
            'nombre' => 'Villa Adela',
            'codigo' => 'VAD',
            'zona_id' => $fernando->id,
            'latitud' => -25.3356,
            'longitud' => -57.5534,
            'descripcion' => 'Barrio Villa Adela'
        ]);
    }
}
