{{-- Global statistics (shown when no RW is selected) --}}
<div>
    <div class="grid grid-cols-2 gap-2 mb-3">
        <div class="stat bg-primary/10 rounded-lg p-3 gap-0">
            <div class="stat-title text-[11px]">Penduduk</div>
            <div class="stat-value text-primary text-base" x-text="formatNumber(globalStats.total_penduduk)">-</div>
        </div>
        <div class="stat bg-secondary/10 rounded-lg p-3 gap-0">
            <div class="stat-title text-[11px]">Keluarga</div>
            <div class="stat-value text-secondary text-base" x-text="formatNumber(globalStats.total_kk)">-</div>
        </div>
        <div class="stat bg-accent/10 rounded-lg p-3 gap-0">
            <div class="stat-title text-[11px]">RW</div>
            <div class="stat-value text-accent text-base" x-text="globalStats.total_rw">-</div>
        </div>
        <div class="stat bg-info/10 rounded-lg p-3 gap-0">
            <div class="stat-title text-[11px]">RT</div>
            <div class="stat-value text-info text-base" x-text="globalStats.total_rt">-</div>
        </div>
    </div>
    <div class="text-xs text-base-content/60 mb-1 flex justify-between">
        <span>👨 <span x-text="formatNumber(globalStats.laki_laki)"></span></span>
        <span>👩 <span x-text="formatNumber(globalStats.perempuan)"></span></span>
    </div>
    <progress class="progress progress-info w-full h-1.5"
        :value="globalStats.total_penduduk ? globalStats.laki_laki : 50"
        :max="globalStats.total_penduduk || 100"></progress>
    <div class="flex justify-between text-[10px] text-base-content/40 mt-0.5">
        <span>Laki-laki</span>
        <span>Perempuan</span>
    </div>
</div>
