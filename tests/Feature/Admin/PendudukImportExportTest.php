<?php

namespace Tests\Feature\Admin;

use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Keluarga;
use App\Models\Penduduk;
use App\Models\Role;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;

class PendudukImportExportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create([
            'name' => Role::ADMIN,
            'label' => 'Admin Sistem',
            'description' => 'Full system access',
            'permissions' => ['*'],
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);
    }

    public function test_penduduk_import_uses_expected_excel_format_and_creates_missing_wilayah(): void
    {
        Kelurahan::factory()->create([
            'kecamatan_id' => Kecamatan::factory(),
            'nama' => 'Batua',
        ]);

        $upload = $this->makeExcelUpload([
            ['NO URUT', 'NO URUT KK', 'NIK', 'NAMA WARGA', 'JENIS KELAMIN', 'UMUR', 'STATUS DALAM KELUARGA', 'PENDIDIKAN', 'PEKERJAAN', 'WILAYAH', 'STATUS', 'KATEGORI'],
            ['1', '1', '7371125201800002', 'ROSALINA PONGANAN', 'P', '46', 'Kepala Rumah Tangga', 'SMU/SMA/Sederajat', 'Lainnya', 'RT 04 / RW 05', 'AKTIF', 'A'],
        ]);

        $response = $this->actingAs($this->admin)->post(route('import-export.import.process', 'penduduk'), [
            'file' => $upload,
        ]);

        $response->assertRedirect(route('kependudukan.penduduk.index'));
        $response->assertSessionHas('success');

        $rw = Rw::where('nomor', 5)->first();
        $this->assertNotNull($rw);

        $rt = Rt::where('rw_id', $rw->id)->where('nomor', 4)->first();
        $this->assertNotNull($rt);

        $this->assertDatabaseHas('penduduks', [
            'nik' => '7371125201800002',
            'nama' => 'ROSALINA PONGANAN',
            'jenis_kelamin' => 'P',
            'pendidikan' => 'SMU/SMA/Sederajat',
            'pekerjaan' => 'Lainnya',
            'gol_darah' => 'A',
            'status_data' => 'aktif',
            'rt_id' => $rt->id,
            'petugas_input_id' => $this->admin->id,
        ]);
    }

    public function test_penduduk_export_uses_expected_excel_format(): void
    {
        $rw = Rw::factory()->create(['nomor' => 5]);
        $rt = Rt::factory()->create([
            'rw_id' => $rw->id,
            'nomor' => 4,
        ]);

        $keluarga = Keluarga::factory()->create([
            'rt_id' => $rt->id,
            'no_kk' => '7371121201800001',
        ]);

        $penduduk = Penduduk::factory()->create([
            'keluarga_id' => $keluarga->id,
            'rt_id' => $rt->id,
            'nik' => '7371125201800002',
            'nama' => 'ROSALINA PONGANAN',
            'jenis_kelamin' => 'P',
            'pendidikan' => 'SMU/SMA/Sederajat',
            'pekerjaan' => 'Lainnya',
            'gol_darah' => 'A',
            'status_data' => 'aktif',
        ]);

        $keluarga->update([
            'kepala_keluarga_id' => $penduduk->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('import-export.export.process', 'penduduk'));

        $response->assertStatus(200);
        $this->assertInstanceOf(BinaryFileResponse::class, $response->baseResponse);

        $rows = $this->readExcelRows($response->baseResponse->getFile()->getPathname());

        $this->assertSame(
            ['NO URUT', 'NO URUT KK', 'NIK', 'NAMA WARGA', 'JENIS KELAMIN', 'UMUR', 'STATUS DALAM KELUARGA', 'PENDIDIKAN', 'PEKERJAAN', 'WILAYAH', 'STATUS', 'KATEGORI'],
            $rows[0]
        );

        $this->assertSame([
            '1',
            '1',
            $penduduk->nik,
            $penduduk->nama,
            'P',
            (string) Carbon::create(1980, 1, 12)->age,
            'Kepala Rumah Tangga',
            'SMU/SMA/Sederajat',
            'Lainnya',
            'RT 04 / RW 05',
            'AKTIF',
            'A',
        ], $rows[1]);
    }

    private function makeExcelUpload(array $rows): UploadedFile
    {
        $path = storage_path('app/testing/' . uniqid('penduduk_import_', true) . '.xlsx');
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $writer = new XlsxWriter();
        $writer->openToFile($path);

        foreach ($rows as $row) {
            $writer->addRow(Row::fromValues($row));
        }

        $writer->close();

        return new UploadedFile(
            $path,
            'penduduk.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
    }

    private function readExcelRows(string $path): array
    {
        $reader = new XlsxReader();
        $reader->open($path);

        $rows = [];
        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rows[] = array_map(
                    fn ($cell) => $cell === null ? '' : (string) $cell,
                    $row->toArray()
                );
            }

            break;
        }

        $reader->close();

        return $rows;
    }
}
