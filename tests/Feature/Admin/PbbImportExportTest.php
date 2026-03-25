<?php

namespace Tests\Feature\Admin;

use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\Pbb;
use App\Models\Role;
use App\Models\Rt;
use App\Models\Rw;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;

class PbbImportExportTest extends TestCase
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

    public function test_pbb_import_uses_expected_excel_format(): void
    {
        $kelurahan = Kelurahan::factory()->create([
            'kecamatan_id' => Kecamatan::factory(),
            'nama' => 'Batua',
        ]);

        $upload = $this->makeExcelUpload([
            ['NO', 'NAMA WAJIB PAJAK', 'RW', 'RT', 'OBJEK PAJAK', 'NOP', 'BEBAN (Rp)', 'STATUS (Lunas/Belum)'],
            ['1', 'Tamrin Marewa', '05', '01', 'Jl. Bontobila 2 RT.01 RW.05', '73.71.140.004.001-0015.0', '77202', 'BELUM'],
        ]);

        $response = $this->actingAs($this->admin)->post(route('import-export.import.process', 'pbb'), [
            'file' => $upload,
        ]);

        $response->assertRedirect(route('data-umum.pbb.index'));
        $response->assertSessionHas('success');

        $rw = Rw::where('kelurahan_id', $kelurahan->id)->where('nomor', 5)->first();
        $this->assertNotNull($rw);

        $rt = Rt::where('rw_id', $rw->id)->where('nomor', 1)->first();
        $this->assertNotNull($rt);

        $this->assertDatabaseHas('pbbs', [
            'kelurahan_id' => $kelurahan->id,
            'rw_id' => $rw->id,
            'rt_id' => $rt->id,
            'nama_wajib_pajak' => 'Tamrin Marewa',
            'objek_pajak' => 'Jl. Bontobila 2 RT.01 RW.05',
            'nob' => '73.71.140.004.001-0015.0',
            'beban' => 77202,
            'status' => 'Belum',
        ]);
    }

    public function test_pbb_export_uses_expected_excel_format(): void
    {
        $rw = Rw::factory()->create(['nomor' => 5]);
        $rt = Rt::factory()->create([
            'rw_id' => $rw->id,
            'nomor' => 1,
        ]);

        Pbb::create([
            'kelurahan_id' => $rw->kelurahan_id,
            'rw_id' => $rw->id,
            'rt_id' => $rt->id,
            'nama_wajib_pajak' => 'Tamrin Marewa',
            'objek_pajak' => 'Jl. Bontobila 2 RT.01 RW.05',
            'nob' => '73.71.140.004.001-0015.0',
            'beban' => 77202,
            'status' => 'Belum',
        ]);

        $response = $this->actingAs($this->admin)->post(route('import-export.export.process', 'pbb'));

        $response->assertStatus(200);
        $this->assertInstanceOf(BinaryFileResponse::class, $response->baseResponse);

        $rows = $this->readExcelRows($response->baseResponse->getFile()->getPathname());

        $this->assertSame(
            ['NO', 'NAMA WAJIB PAJAK', 'RW', 'RT', 'OBJEK PAJAK', 'NOP', 'BEBAN (Rp)', 'STATUS (Lunas/Belum)'],
            $rows[0]
        );

        $this->assertSame([
            '1',
            'Tamrin Marewa',
            '05',
            '01',
            'Jl. Bontobila 2 RT.01 RW.05',
            '73.71.140.004.001-0015.0',
            '77202',
            'BELUM',
        ], $rows[1]);
    }

    private function makeExcelUpload(array $rows): UploadedFile
    {
        $path = storage_path('app/testing/' . uniqid('pbb_import_', true) . '.xlsx');
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
            'pbb.xlsx',
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
