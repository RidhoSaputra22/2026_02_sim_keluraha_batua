{{-- Clickable RW list in sidebar --}}
<div class="px-2 pb-3 h-96 overflow-scroll">
    <template x-for="rw in sortedRwList" :key="rw.name">
        <div class="rw-list-item rounded-lg px-3 py-2.5 mb-1" :class="{ 'active': selectedRw === rw.name }"
            @click="selectRw(rw.name)">
            <div class="flex items-center gap-3">
                <div class="rw-swatch" :style="`background-color: ${rw.warna || rwColors[rw.name] || '#6b7280'}`"></div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-xs" x-text="rw.name"></span>
                        <span class="text-xs text-base-content/50"
                            x-text="formatNumber(rw.total_penduduk) + ' jiwa'"></span>
                    </div>
                    <div class="flex items-center gap-1.5 mt-1">
                        <x-ui.badge type="ghost" size="xs"><span x-text="rw.total_rt + ' RT'"></span></x-ui.badge>
                        <x-ui.badge type="ghost" size="xs"><span x-text="formatNumber(rw.total_kk) + ' KK'"></span>
                        </x-ui.badge>
                        <x-ui.badge type="ghost" size="xs"><span x-text="formatNumber(rw.total_umkm) + ' UMKM'"></span>
                        </x-ui.badge>
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-base-content/30 flex-shrink-0" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </div>
    </template>
</div>
