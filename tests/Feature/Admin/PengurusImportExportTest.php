<?php

namespace Tests\Feature\Admin;

use App\Enums\StatusAktifEnum;
use App\Models\JabatanRtRw;
use App\Models\Kelurahan;
use App\Models\Kecamatan;
use App\Models\Penduduk;
use App\Models\Role;
use App\Models\Rt;
use App\Models\RtRwPengurus;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;

class PengurusImportExportTest extends TestCase
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

    public function test_pengurus_import_creates_missing_rw_rt_and_links_pengurus(): void
    {
        $kelurahan = Kelurahan::factory()->create([
            'kecamatan_id' => Kecamatan::factory(),
            'nama' => 'Batua',
        ]);

        JabatanRtRw::create(['nama' => 'Ketua RT']);

        $upload = $this->makeExcelUpload([
            ['RW', 'RT', 'NAMA', 'NIK', 'ALAMAT', 'PEKERJAAN', 'PENDIDIKAN TERAKHIR', 'NO. TLP', 'KET'],
            ['7', '3', 'DR. YOHANIS SATTU', '7371121404680011', 'JL. INSPEKSI PAM LR. 4 NO.17', 'PNS', 'S3', '08121262294', ''],
        ]);

        $response = $this->actingAs($this->admin)->post(route('import-export.import.process', 'pengurus'), [
            'file' => $upload,
        ]);

        $response->assertRedirect(route('master.pengurus.index'));
        $response->assertSessionHas('success');

        $rw = Rw::where('kelurahan_id', $kelurahan->id)->where('nomor', 7)->first();
        $this->assertNotNull($rw);

        $rt = Rt::where('rw_id', $rw->id)->where('nomor', 3)->first();
        $this->assertNotNull($rt);

        $penduduk = Penduduk::where('nik', '7371121404680011')->first();
        $this->assertNotNull($penduduk);
        $this->assertSame('DR. YOHANIS SATTU', $penduduk->nama);
        $this->assertSame('JL. INSPEKSI PAM LR. 4 NO.17', $penduduk->alamat);
        $this->assertSame('PNS', $penduduk->pekerjaan);
        $this->assertSame('S3', $penduduk->pendidikan);
        $this->assertSame($rt->id, $penduduk->rt_id);

        $this->assertDatabaseHas('rt_rw_pengurus', [
            'kelurahan_id' => $kelurahan->id,
            'penduduk_id' => $penduduk->id,
            'rw_id' => $rw->id,
            'rt_id' => $rt->id,
            'alamat' => 'JL. INSPEKSI PAM LR. 4 NO.17',
            'no_telp' => '08121262294',
            'status' => StatusAktifEnum::AKTIF->value,
        ]);
    }

    public function test_pengurus_export_uses_expected_excel_format(): void
    {
        $jabatan = JabatanRtRw::create(['nama' => 'Ketua RT']);
        $rw = Rw::factory()->create(['nomor' => 4]);
        $rt = Rt::factory()->create([
            'rw_id' => $rw->id,
            'nomor' => 2,
        ]);
        $penduduk = Penduduk::factory()->create([
            'rt_id' => $rt->id,
            'nama' => 'Agus Salim',
            'nik' => '7371112807830040',
            'alamat' => 'Jl. Batua Raya No. 10',
            'pekerjaan' => 'Wiraswasta',
            'pendidikan' => 'S1',
        ]);

        $pengurus = RtRwPengurus::create([
            'kelurahan_id' => $rw->kelurahan_id,
            'penduduk_id' => $penduduk->id,
            'jabatan_id' => $jabatan->id,
            'rw_id' => $rw->id,
            'rt_id' => $rt->id,
            'status' => StatusAktifEnum::AKTIF->value,
            'alamat' => 'Jl. Batua Raya No. 10',
            'no_telp' => '08113858806',
        ]);

        $response = $this->actingAs($this->admin)->post(route('import-export.export.process', 'pengurus'));

        $response->assertStatus(200);
        $this->assertInstanceOf(BinaryFileResponse::class, $response->baseResponse);

        $rows = $this->readExcelRows($response->baseResponse->getFile()->getPathname());

        $this->assertSame(
            ['RW', 'RT', 'NAMA', 'NIK', 'ALAMAT', 'PEKERJAAN', 'PENDIDIKAN TERAKHIR', 'NO. TLP', 'KET'],
            $rows[0]
        );

        $this->assertSame([
            (string) $rw->nomor,
            (string) $rt->nomor,
            $penduduk->nama,
            $penduduk->nik,
            $pengurus->alamat,
            $penduduk->pekerjaan,
            $penduduk->pendidikan,
            $pengurus->no_telp,
            '',
        ], $rows[1]);
    }

    private function makeExcelUpload(array $rows): UploadedFile
    {
        $path = storage_path('app/testing/' . uniqid('pengurus_import_', true) . '.xlsx');
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
            'pengurus.xlsx',
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
