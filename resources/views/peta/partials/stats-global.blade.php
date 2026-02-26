{{-- Global statistics (shown when no RW is selected) --}}
<div>
    <div class="grid grid-cols-2 gap-2 mb-3">
        <div class="stat-mini bg-primary/10">
            <span class="stat-icon">👥</span>
            <div>
                <div class="stat-num text-primary" x-text="formatNumber(globalStats.total_penduduk)">-</div>
                <div class="stat-lbl">Penduduk</div>
            </div>
        </div>
        <div class="stat-mini bg-secondary/10">
            <span class="stat-icon">🏠</span>
            <div>
                <div class="stat-num text-secondary" x-text="formatNumber(globalStats.total_kk)">-</div>
                <div class="stat-lbl">Keluarga</div>
            </div>
        </div>
        <div class="stat-mini bg-accent/10">
            <span class="stat-icon">📍</span>
            <div>
                <div class="stat-num text-accent" x-text="globalStats.total_rw">-</div>
                <div class="stat-lbl">RW</div>
            </div>
        </div>
        <div class="stat-mini bg-info/10">
            <span class="stat-icon">🏘️</span>
            <div>
                <div class="stat-num text-info" x-text="globalStats.total_rt">-</div>
                <div class="stat-lbl">RT</div>
            </div>
        </div>
    </div>
    <div class="text-xs text-base-content/60 mb-1 flex justify-between">
        <span>👨 <span x-text="formatNumber(globalStats.laki_laki)"></span></span>
        <span>👩 <span x-text="formatNumber(globalStats.perempuan)"></span></span>
    </div>
    <div class="gender-bar">
        <div class="gender-bar-fill bg-blue-500"
             :style="`width: ${globalStats.total_penduduk ? ((globalStats.laki_laki / globalStats.total_penduduk) * 100) : 50}%`">
        </div>
    </div>
    <div class="flex justify-between text-[10px] text-base-content/40 mt-0.5">
        <span>Laki-laki</span>
        <span>Perempuan</span>
    </div>
</div>
