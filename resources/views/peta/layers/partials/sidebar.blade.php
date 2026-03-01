{{-- RIGHT: Layer management sidebar (QGIS-style) --}}
<div class="layer-sidebar w-full lg:w-80 xl:w-96 bg-base-100 border-t lg:border-t-0 lg:border-l border-base-200 flex flex-col">

    {{-- Header --}}
    <div class="p-3 border-b border-base-200 flex-shrink-0">
        <div class="flex items-center justify-between">
            <h3 class="font-bold text-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                Layer
                <span class="badge badge-ghost badge-xs" x-text="layers.length + ' layer'"></span>
            </h3>
            <button class="btn btn-primary btn-xs" @click="openNewLayerModal()" title="Tambah Layer Baru">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah
            </button>
        </div>
    </div>

    {{-- Layer List (drag-reorderable) --}}
    <div class="flex-1 overflow-y-auto" id="layer-list-container">
        <div class="px-1 py-2">
            <template x-for="(layer, index) in layers" :key="layer.id">
                <div class="layer-item rounded-lg px-2 py-2 mb-0.5 mx-1"
                    :class="{ 'active': activeLayer?.id === layer.id }"
                    draggable="true"
                    @dragstart="onDragStart($event, index)"
                    @dragover.prevent="onDragOver($event, index)"
                    @dragleave="onDragLeave($event)"
                    @drop="onDrop($event, index)"
                    @dragend="onDragEnd($event)">

                    <div class="flex items-center gap-2">
                        {{-- Drag handle --}}
                        <div class="drag-handle flex-shrink-0" title="Seret untuk mengubah urutan">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                            </svg>
                        </div>

                        {{-- Visibility toggle --}}
                        <button class="flex-shrink-0" @click.stop="toggleLayerVisibility(layer)" :title="layer.visible ? 'Sembunyikan' : 'Tampilkan'">
                            <svg x-show="layer.visible" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-base-content/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="!layer.visible" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-base-content/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>

                        {{-- Color dot --}}
                        <div class="layer-color-dot flex-shrink-0" :style="'background-color:' + layer.warna"></div>

                        {{-- Layer name (click to select) --}}
                        <div class="flex-1 min-w-0" @click="selectLayer(layer)">
                            <div class="flex items-center gap-1.5">
                                <span class="text-xs font-semibold truncate" x-text="layer.nama"></span>
                                <span class="badge badge-ghost" style="font-size: 9px; padding: 0 4px; height: 14px;" x-text="layer.polygons_count + 'p'"></span>
                            </div>
                        </div>

                        {{-- Layer context menu --}}
                        <div class="dropdown dropdown-end flex-shrink-0" @click.stop>
                            <button tabindex="0" class="btn btn-ghost btn-xs btn-circle opacity-50 hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                </svg>
                            </button>
                            <ul tabindex="0" class="dropdown-content menu menu-xs bg-base-100 rounded-lg shadow-xl border border-base-200 w-44 z-50">
                                <li><a @click="selectLayer(layer)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                    Edit Polygon
                                </a></li>
                                <li><a @click="openEditLayerModal(layer)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    Pengaturan
                                </a></li>
                                <li><a @click="toggleActive(layer)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span x-text="layer.is_active ? 'Nonaktifkan' : 'Aktifkan'"></span>
                                </a></li>
                                <li><a @click="zoomToLayer(layer)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" /></svg>
                                    Zoom ke Layer
                                </a></li>
                                <div class="divider my-0"></div>
                                <li><a class="text-error" @click="deleteLayer(layer)">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    Hapus Layer
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Empty state --}}
            <template x-if="layers.length === 0">
                <div class="text-center py-8 px-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-base-content/20 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    <p class="text-xs text-base-content/50 mb-3">Belum ada layer.</p>
                    <button class="btn btn-primary btn-xs" @click="openNewLayerModal()">Buat Layer Pertama</button>
                </div>
            </template>
        </div>
    </div>

    {{-- Polygon List panel (shown when a layer is selected) --}}
    <template x-if="activeLayer">
        <div class="border-t border-base-200 flex-shrink-0">
            <div class="p-3">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-xs font-semibold text-base-content/60 uppercase tracking-wider flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6z" />
                        </svg>
                        Polygon
                    </h4>
                    <span class="badge badge-primary badge-xs" x-text="activePolygonCount"></span>
                </div>

                <div class="space-y-1 max-h-48 overflow-y-auto">
                    <template x-for="(poly, idx) in polygonList" :key="poly.id || idx">
                        <div class="polygon-item flex items-center gap-2 px-2 py-1.5 rounded-md">
                            <div class="w-3 h-3 rounded-sm flex-shrink-0" :style="'background-color:' + (activeLayer?.warna || '#6b7280')"></div>
                            <input type="text" class="input input-bordered input-xs flex-1 min-w-0"
                                :value="poly.nama || 'Polygon ' + (idx + 1)"
                                @change="updatePolygonName(poly, $event.target.value)"
                                placeholder="Nama polygon..." @click.stop>
                            <button class="btn btn-ghost btn-xs btn-circle opacity-50 hover:opacity-100"
                                @click.stop="zoomToPolygon(poly)" title="Zoom">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                            <button class="btn btn-ghost btn-xs btn-circle text-error opacity-50 hover:opacity-100"
                                @click.stop="deletePolygon(poly, idx)" title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </template>
                    <template x-if="polygonList.length === 0">
                        <p class="text-[11px] text-base-content/40 text-center py-2">
                            Gunakan toolbar gambar di peta untuk menambah polygon.
                        </p>
                    </template>
                </div>
            </div>
        </div>
    </template>

    {{-- Footer --}}
    <div class="p-2 border-t border-base-200 flex-shrink-0">
        <div class="flex items-center justify-between text-xs text-base-content/40">
            <span>Seret layer untuk mengubah urutan</span>
        </div>
    </div>
</div>
