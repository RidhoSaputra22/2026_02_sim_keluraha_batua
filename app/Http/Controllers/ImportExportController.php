<?php

namespace App\Http\Controllers;

use App\Enums\StatusAktifEnum;
use App\Models\JabatanRtRw;
use App\Models\Kelurahan;
use App\Models\Penduduk;
use App\Models\Rt;
use App\Models\RtRwPengurus;
use App\Models\Rw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImportExportController extends Controller
{
    protected ?int $defaultPengurusKelurahanId = null;

    /**
     * Lookup kelurahan_id dari nama kelurahan
     */
    protected function lookup_kelurahan_id($nama)
    {
        $nama = $this->normalizeTextValue($nama);

        if (! $nama) {
            return null;
        }

        return Kelurahan::where('nama', $nama)->value('id');
    }

    /**
     * Lookup rw_id dari nomor RW dan kelurahan (harus sudah resolve kelurahan_id di $row)
     */
    protected function lookup_rw_id($nomor, $row)
    {
        $nomor = $this->normalizeWilayahNomor($nomor);

        if (! $nomor || empty($row['kelurahan_id'])) {
            return null;
        }

        return Rw::where('nomor', $nomor)
            ->where('kelurahan_id', $row['kelurahan_id'])
            ->value('id');
    }

    /**
     * Lookup rt_id dari nomor RT dan rw (harus sudah resolve rw_id di $row)
     */
    protected function lookup_rt_id($nomor, $row)
    {
        $nomor = $this->normalizeWilayahNomor($nomor);

        if (! $nomor || empty($row['rw_id'])) {
            return null;
        }

        return Rt::where('nomor', $nomor)
            ->where('rw_id', $row['rw_id'])
            ->value('id');
    }

    /**
     * Ambil konfigurasi modul dari config/import-export.php
     */
    protected function getModuleConfig(string $module): array
    {
        $config = config("import-export.{$module}");

        if (! $config) {
            abort(404, "Modul '{$module}' tidak ditemukan.");
        }

        return $config;
    }

    /**
     * Resolve nilai kolom dari model (termasuk relasi dot-notation)
     */
    protected function resolveValue($model, string $column, array $resolvers): mixed
    {
        if (isset($resolvers[$column])) {
            $path = $resolvers[$column];

            if ($path === 'rt') {
                return $this->getRtLabel($model);
            }

            if ($path === 'rw') {
                return $this->getRwLabel($model);
            }

            if ($path === 'kelurahan') {
                return $this->getKelurahanLabel($model);
            }

            if ($path === 'pengurus_keterangan') {
                return $this->getPengurusKeteranganLabel($model);
            }

            return data_get($model, $path, '-');
        }

        return $model->{$column} ?? '-';
    }

    /**
     * Generate label RT
     */
    protected function getRtLabel($model): string
    {
        $rt = $model->rt ?? null;
        if (! $rt) {
            return '-';
        }

        return str_pad((string) ($rt->nomor ?? '-'), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Generate label RW
     */
    protected function getRwLabel($model): string
    {
        $rw = $model->rw ?? null;
        if (! $rw) {
            return '-';
        }

        return str_pad((string) ($rw->nomor ?? '-'), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Generate label Kelurahan
     */
    protected function getKelurahanLabel($model): string
    {
        $kelurahan = $model->kelurahan ?? null;
        if (! $kelurahan) {
            return '-';
        }

        return $kelurahan->nama ?? '-';
    }

    protected function getPengurusKeteranganLabel($model): string
    {
        $jabatan = trim((string) data_get($model, 'jabatan.nama', ''));

        if (in_array(Str::lower($jabatan), ['ketua rt', 'ketua rw'], true)) {
            return '';
        }

        return $jabatan;
    }

    // ═══════════════════════════════════════════════════════════════
    //  EXPORT
    // ═══════════════════════════════════════════════════════════════

    /**
     * Tampilkan form export (pilih rentang tanggal, format, dll)
     */
    public function exportForm(string $module)
    {
        $config = $this->getModuleConfig($module);

        return view('import-export.export', [
            'module'     => $module,
            'config'     => $config,
            'title'      => $config['title'],
            'backRoute'  => $config['back_route'],
            'dateColumn' => $config['date_column'],
        ]);
    }

    /**
     * Proses export dan download file Excel (.xlsx)
     */
    public function export(Request $request, string $module): BinaryFileResponse
    {
        $config = $this->getModuleConfig($module);

        $request->validate([
            'tanggal_dari'   => ['nullable', 'date'],
            'tanggal_sampai' => ['nullable', 'date', 'after_or_equal:tanggal_dari'],
        ]);

        $modelClass = $config['model'];
        $dateColumn = $config['date_column'];
        $query      = $modelClass::query();

        if (! empty($config['with'])) {
            $query->with($config['with']);
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate($dateColumn, '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate($dateColumn, '<=', $request->tanggal_sampai);
        }

        $records   = $query->get();
        $headers   = $config['headers'];
        $columns   = $config['columns'];
        $resolvers = $config['resolvers'] ?? [];
        $filename  = 'export_' . str_replace('-', '_', $module) . '_' . now()->format('Ymd_His') . '.xlsx';
        $tempPath  = storage_path('app/' . $filename);

        $writer = new XlsxWriter();
        $writer->openToFile($tempPath);

        $headerStyle = (new Style())->withFontBold(true)->withFontSize(11);
        $writer->addRow(Row::fromValuesWithStyle($headers, $headerStyle));

        foreach ($records as $record) {
            $row = [];
            foreach ($columns as $col) {
                $value = $this->resolveValue($record, $col, $resolvers);
                $row[] = is_null($value) ? '' : (string) $value;
            }

            $writer->addRow(Row::fromValues($row));
        }

        $writer->close();

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    // ═══════════════════════════════════════════════════════════════
    //  IMPORT
    // ═══════════════════════════════════════════════════════════════

    /**
     * Tampilkan form import (upload file, download template)
     */
    public function importForm(string $module)
    {
        $config = $this->getModuleConfig($module);

        return view('import-export.import', [
            'module'        => $module,
            'config'        => $config,
            'title'         => $config['title'],
            'backRoute'     => $config['back_route'],
            'required'      => $config['required'] ?? [],
            'importHeaders' => $config['import_headers'] ?? $config['headers'],
            'importColumns' => $config['import_columns'] ?? $config['columns'],
        ]);
    }

    /**
     * Download template Excel (.xlsx) kosong untuk import
     */
    public function downloadTemplate(string $module): BinaryFileResponse
    {
        $config   = $this->getModuleConfig($module);
        $headers  = $config['import_headers'] ?? $config['headers'];
        $filename = 'template_import_' . str_replace('-', '_', $module) . '.xlsx';
        $tempPath = storage_path('app/' . $filename);

        $writer = new XlsxWriter();
        $writer->openToFile($tempPath);

        $headerStyle = (new Style())->withFontBold(true)->withFontSize(11);
        $writer->addRow(Row::fromValuesWithStyle($headers, $headerStyle));
        $writer->addRow(Row::fromValues($this->buildTemplateExampleRow($headers, $config)));

        $writer->close();

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Proses import dari file Excel (.xlsx) yang diupload
     */
    public function import(Request $request, string $module)
    {
        $config = $this->getModuleConfig($module);

        $request->validate([
            'file'        => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
            'skip_errors' => ['nullable', 'boolean'],
        ]);

        $modelClass    = $config['model'];
        $importColumns = $config['import_columns'] ?? $config['columns'];
        $required      = $config['required'] ?? [];
        $importers     = $config['importers'] ?? [];
        $skipErrors    = $request->boolean('skip_errors', false);

        $file   = $request->file('file');
        $reader = new XlsxReader();

        try {
            $reader->open($file->getRealPath());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membaca file Excel. Pastikan format file benar.');
        }

        $imported = 0;
        $skipped  = 0;
        $errors   = [];
        $rowNum   = 0;
        $isHeader = true;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rowNum++;

                if ($isHeader) {
                    $isHeader = false;
                    continue;
                }

                $cells = $row->toArray();

                if (collect($cells)->filter(fn ($value) => ! empty($value))->isEmpty()) {
                    continue;
                }

                if (count($cells) < count($importColumns)) {
                    $cells = array_pad($cells, count($importColumns), '');
                }

                $data = [];
                foreach ($importColumns as $i => $col) {
                    $data[$col] = $this->normalizeImportValue($cells[$i] ?? null, $col);
                }

                $data = $this->transformImportData($module, $data, $importers);

                $missingFields = [];
                foreach ($required as $field) {
                    if (in_array($field, $importColumns, true) && empty($data[$field])) {
                        $missingFields[] = $field;
                    }
                }

                if (! empty($missingFields)) {
                    $errors[] = "Baris {$rowNum}: Kolom wajib kosong (" . implode(', ', $missingFields) . ')';
                    if (! $skipErrors) {
                        $reader->close();

                        return redirect()->back()
                            ->with('error', "Import gagal pada baris {$rowNum}: Kolom wajib kosong (" . implode(', ', $missingFields) . ')')
                            ->with('import_errors', $errors);
                    }

                    continue;
                }

                if ($module !== 'pengurus' && $this->rowAlreadyExists($modelClass, $data, $config)) {
                    $skipped++;
                    continue;
                }

                try {
                    $result = $this->persistImportedRow($module, $modelClass, $data);

                    if ($result === 'skipped') {
                        $skipped++;
                    } else {
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $errorMsg = "Baris {$rowNum}: " . $this->cleanErrorMessage($e->getMessage());
                    $errors[] = $errorMsg;

                    if (! $skipErrors) {
                        $reader->close();

                        return redirect()->back()
                            ->with('error', "Import gagal pada baris {$rowNum}: " . $this->cleanErrorMessage($e->getMessage()))
                            ->with('import_errors', $errors);
                    }
                }
            }

            break;
        }

        $reader->close();

        $message = "Berhasil mengimport {$imported} data {$config['title']}.";
        if ($skipped > 0) {
            $message .= " ({$skipped} data sudah ada, dilewati)";
        }
        if (! empty($errors)) {
            $message .= ' (' . count($errors) . ' baris gagal)';
        }

        return redirect()->route($config['back_route'])
            ->with('success', $message)
            ->with('import_errors', $errors);
    }

    protected function buildTemplateExampleRow(array $headers, array $config): array
    {
        if (! empty($config['example_row'])) {
            return array_pad($config['example_row'], count($headers), '');
        }

        return array_map(function ($header) {
            if (preg_match('/\bkelurahan\b/i', $header)) {
                return 'Batua';
            }

            if (preg_match('/\bRW\b/i', $header)) {
                return '01';
            }

            if (preg_match('/\bRT\b/i', $header)) {
                return '01';
            }

            return '';
        }, $headers);
    }

    protected function normalizeImportValue(mixed $value, string $column): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_string($value)) {
            $value = trim($value);

            return $value === '' ? null : $value;
        }

        if (is_int($value)) {
            return in_array($column, ['nik', 'no_telp', 'rw', 'rt'], true)
                ? (string) $value
                : $value;
        }

        if (is_float($value)) {
            if (fmod($value, 1.0) === 0.0) {
                return in_array($column, ['nik', 'no_telp', 'rw', 'rt'], true)
                    ? number_format($value, 0, '.', '')
                    : (int) $value;
            }

            return rtrim(rtrim(number_format($value, 10, '.', ''), '0'), '.');
        }

        return $value;
    }

    protected function transformImportData(string $module, array $data, array $importers): array
    {
        if ($module === 'pengurus') {
            $data['rw'] = $this->normalizeWilayahNomor($data['rw'] ?? null);
            $data['rt'] = $this->normalizeWilayahNomor($data['rt'] ?? null);
            $data['nik'] = $this->normalizeTextValue($data['nik'] ?? null);
            $data['nama'] = $this->normalizeTextValue($data['nama'] ?? null);
            $data['alamat'] = $this->normalizeTextValue($data['alamat'] ?? null);
            $data['pekerjaan'] = $this->normalizeTextValue($data['pekerjaan'] ?? null);
            $data['pendidikan'] = $this->normalizeTextValue($data['pendidikan'] ?? null);
            $data['no_telp'] = $this->normalizeTextValue($data['no_telp'] ?? null);
            $data['keterangan'] = $this->normalizeTextValue($data['keterangan'] ?? null);

            return $data;
        }

        if (isset($importers['kelurahan_id'])) {
            $data['kelurahan_id'] = $this->lookup_kelurahan_id($data['kelurahan'] ?? null);
        }

        if (isset($importers['rw_id'])) {
            $data['rw_id'] = $this->lookup_rw_id($data['rw'] ?? null, $data);
        }

        if (isset($importers['rt_id'])) {
            $data['rt_id'] = $this->lookup_rt_id($data['rt'] ?? null, $data);
        }

        unset($data['kelurahan'], $data['rw'], $data['rt']);

        return $data;
    }

    protected function rowAlreadyExists(string $modelClass, array $data, array $config): bool
    {
        $uniqueBy = $config['unique_by'] ?? [];

        if (! $uniqueBy) {
            return false;
        }

        $query = $modelClass::query();

        foreach ($uniqueBy as $col) {
            $query->where($col, $data[$col] ?? null);
        }

        return $query->exists();
    }

    protected function persistImportedRow(string $module, string $modelClass, array $data): string
    {
        if ($module === 'pengurus') {
            return $this->persistPengurusImportRow($data);
        }

        $modelClass::create($data);

        return 'created';
    }

    protected function persistPengurusImportRow(array $data): string
    {
        return DB::transaction(function () use ($data) {
            $kelurahanId = $this->resolveDefaultPengurusKelurahanId();

            if (! $kelurahanId) {
                throw new \RuntimeException('Data kelurahan belum tersedia untuk mengaitkan RW/RT.');
            }

            $rwNomor = $this->normalizeWilayahNomor($data['rw'] ?? null);
            if (! $rwNomor) {
                throw new \RuntimeException('Nomor RW wajib diisi.');
            }

            $rw = Rw::firstOrCreate([
                'kelurahan_id' => $kelurahanId,
                'nomor' => $rwNomor,
            ]);

            $jabatan = $this->resolvePengurusJabatan($data);
            $rt = null;

            if ($this->jabatanMemerlukanRt($jabatan)) {
                $rtNomor = $this->normalizeWilayahNomor($data['rt'] ?? null);

                if (! $rtNomor) {
                    throw new \RuntimeException('Nomor RT wajib diisi untuk jabatan level RT.');
                }

                $rt = Rt::firstOrCreate([
                    'rw_id' => $rw->id,
                    'nomor' => $rtNomor,
                ]);
            }

            $penduduk = $this->upsertPendudukDariImportPengurus($data, $rt?->id);

            $existingPengurus = RtRwPengurus::query()
                ->where('penduduk_id', $penduduk->id)
                ->where('jabatan_id', $jabatan->id)
                ->where('rw_id', $rw->id)
                ->where('rt_id', $rt?->id)
                ->first();

            if ($existingPengurus) {
                return 'skipped';
            }

            RtRwPengurus::create([
                'kelurahan_id' => $kelurahanId,
                'penduduk_id' => $penduduk->id,
                'jabatan_id' => $jabatan->id,
                'rw_id' => $rw->id,
                'rt_id' => $rt?->id,
                'tgl_mulai' => null,
                'status' => $this->resolvePengurusStatus($data['keterangan'] ?? null),
                'alamat' => $data['alamat'] ?? $penduduk->alamat,
                'no_telp' => $data['no_telp'] ?? null,
            ]);

            return 'created';
        });
    }

    protected function resolveDefaultPengurusKelurahanId(): ?int
    {
        if ($this->defaultPengurusKelurahanId !== null) {
            return $this->defaultPengurusKelurahanId;
        }

        $this->defaultPengurusKelurahanId = Rw::query()
            ->select('kelurahan_id')
            ->distinct()
            ->orderBy('kelurahan_id')
            ->value('kelurahan_id');

        if ($this->defaultPengurusKelurahanId === null) {
            $this->defaultPengurusKelurahanId = Kelurahan::query()
                ->orderBy('id')
                ->value('id');
        }

        return $this->defaultPengurusKelurahanId;
    }

    protected function resolvePengurusJabatan(array $data): JabatanRtRw
    {
        $keterangan = $this->normalizeTextValue($data['keterangan'] ?? null);

        if ($keterangan) {
            $jabatan = JabatanRtRw::query()
                ->whereRaw('LOWER(nama) = ?', [Str::lower($keterangan)])
                ->first();

            if ($jabatan) {
                return $jabatan;
            }
        }

        $jabatanDefault = ! empty($data['rt']) ? 'Ketua RT' : 'Ketua RW';

        return JabatanRtRw::firstOrCreate([
            'nama' => $jabatanDefault,
        ]);
    }

    protected function jabatanMemerlukanRt(JabatanRtRw $jabatan): bool
    {
        return (bool) preg_match('/\bRT\b/i', $jabatan->nama);
    }

    protected function resolvePengurusStatus(?string $keterangan): string
    {
        $keterangan = Str::lower(trim((string) $keterangan));

        if ($keterangan !== '' && str_contains($keterangan, 'nonaktif')) {
            return StatusAktifEnum::NONAKTIF->value;
        }

        return StatusAktifEnum::AKTIF->value;
    }

    protected function upsertPendudukDariImportPengurus(array $data, ?int $rtId): Penduduk
    {
        $nik = $this->normalizeTextValue($data['nik'] ?? null);

        if (! $nik) {
            throw new \RuntimeException('NIK wajib diisi untuk data pengurus.');
        }

        $penduduk = Penduduk::firstOrNew(['nik' => $nik]);
        $penduduk->nama = $data['nama'] ?? $penduduk->nama;

        if (! empty($data['alamat'])) {
            $penduduk->alamat = $data['alamat'];
        }

        if (! empty($data['pendidikan'])) {
            $penduduk->pendidikan = $data['pendidikan'];
        }

        if (! empty($data['pekerjaan'])) {
            $penduduk->pekerjaan = $data['pekerjaan'];
        }

        if ($rtId) {
            $penduduk->rt_id = $rtId;
        }

        $penduduk->save();

        return $penduduk;
    }

    protected function normalizeWilayahNomor(mixed $value): ?int
    {
        $value = $this->normalizeTextValue($value);

        if (! $value) {
            return null;
        }

        if (preg_match('/\d+/', $value, $matches) !== 1) {
            return null;
        }

        $nomor = (int) $matches[0];

        return $nomor > 0 ? $nomor : null;
    }

    protected function normalizeTextValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $value = trim($value);

            return $value === '' ? null : $value;
        }

        if (is_int($value)) {
            return (string) $value;
        }

        if (is_float($value)) {
            if (fmod($value, 1.0) === 0.0) {
                return number_format($value, 0, '.', '');
            }

            return rtrim(rtrim(number_format($value, 10, '.', ''), '0'), '.');
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * Bersihkan pesan error agar lebih user-friendly
     */
    protected function cleanErrorMessage(string $message): string
    {
        if (str_contains($message, 'Duplicate entry')) {
            preg_match("/Duplicate entry '(.+?)' for key/", $message, $matches);

            return 'Data duplikat: ' . ($matches[1] ?? 'tidak diketahui');
        }

        if (str_contains($message, 'SQLSTATE')) {
            $parts = explode(']: ', $message);

            return end($parts);
        }

        return Str::limit($message, 120);
    }
}
