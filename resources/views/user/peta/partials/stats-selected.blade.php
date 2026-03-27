{{-- Selected RW statistics (shown when an RW is clicked) --}}
<div>
    <div class="grid grid-cols-2 gap-2 mb-3">
        <div class="stat bg-primary/10 rounded-lg p-3 gap-0">
            <div class="stat-title text-[11px]">Penduduk</div>
            <div class="stat-value text-primary text-base" x-text="formatNumber(selectedStats.total_penduduk)"></div>
        </div>
        <div class="stat bg-secondary/10 rounded-lg p-3 gap-0">
            <div class="stat-title text-[11px]">Keluarga</div>
            <div class="stat-value text-secondary text-base" x-text="formatNumber(selectedStats.total_kk)"></div>
        </div>
        <div class="stat bg-accent/10 rounded-lg p-3 gap-0">
            <div class="stat-title text-[11px]">RT</div>
            <div class="stat-value text-accent text-base" x-text="selectedStats.total_rt"></div>
        </div>
        <div class="stat bg-info/10 rounded-lg p-3 gap-0">
            <div class="stat-title text-[11px]">UMKM</div>
            <div class="stat-value text-info text-base" x-text="formatNumber(selectedStats.total_umkm)"></div>
        </div>
    </div>
    <div class="text-xs text-base-content/60 mb-1 flex justify-between">
        <span>👨 <span x-text="formatNumber(selectedStats.laki_laki)"></span></span>
        <span>👩 <span x-text="formatNumber(selectedStats.perempuan)"></span></span>
    </div>
    <progress class="progress progress-info w-full h-1.5"
        :value="(selectedStats.laki_laki + selectedStats.perempuan) ? selectedStats.laki_laki : 50"
        :max="(selectedStats.laki_laki + selectedStats.perempuan) || 100"></progress>
    <div class="flex justify-between text-[10px] text-base-content/40 mt-0.5">
        <span>Laki-laki</span>
        <span>Perempuan</span>
    </div>
</div>
