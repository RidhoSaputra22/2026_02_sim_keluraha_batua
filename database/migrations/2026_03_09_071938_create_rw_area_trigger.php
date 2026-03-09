<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('
        CREATE OR REPLACE FUNCTION update_rw_luas_area()
        RETURNS trigger AS $$
        BEGIN

            -- jika rw_id kosong, tidak lakukan apa apa
            IF COALESCE(NEW.rw_id, OLD.rw_id) IS NULL THEN
                RETURN NEW;
            END IF;

            -- jika RW tidak ada di tabel rws
            IF NOT EXISTS (
                SELECT 1 FROM rws
                WHERE id = COALESCE(NEW.rw_id, OLD.rw_id)
            ) THEN
                RETURN NEW;
            END IF;

            UPDATE rws
            SET luas_area = (
                SELECT COALESCE(
                    ST_Area(ST_Union(polygon)::geography),
                    0
                )
                FROM peta_layer_polygons
                WHERE rw_id = COALESCE(NEW.rw_id, OLD.rw_id)
            )
            WHERE id = COALESCE(NEW.rw_id, OLD.rw_id);

            RETURN NEW;

        END;
        $$ LANGUAGE plpgsql;
        ');

        DB::unprepared('
        CREATE TRIGGER trg_update_rw_luas_area
        AFTER INSERT OR UPDATE OR DELETE
        ON peta_layer_polygons
        FOR EACH ROW
        EXECUTE FUNCTION update_rw_luas_area();
        ');
    }

    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_update_rw_luas_area ON peta_layer_polygons;");
        DB::unprepared("DROP FUNCTION IF EXISTS update_rw_luas_area;");
    }
};
