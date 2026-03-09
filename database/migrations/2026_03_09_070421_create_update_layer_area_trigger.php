<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('
       CREATE OR REPLACE FUNCTION update_layer_area()
RETURNS trigger AS $$
BEGIN

    IF NEW.polygon IS NOT NULL THEN
        NEW.area := ST_Area(NEW.polygon::geography);
    ELSE
        NEW.area := NULL;
    END IF;

    RETURN NEW;

END;
$$ LANGUAGE plpgsql;
        ');

        DB::unprepared('
       CREATE TRIGGER trg_update_layer_area
BEFORE INSERT OR UPDATE
ON peta_layer_polygons
FOR EACH ROW
EXECUTE FUNCTION update_layer_area();
    ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_update_layer_area ON peta_layer_polygons;');
        DB::unprepared('DROP FUNCTION IF EXISTS update_layer_area;');
    }
};
