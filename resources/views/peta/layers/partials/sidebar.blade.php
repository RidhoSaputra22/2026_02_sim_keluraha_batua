{{-- RIGHT: Layer management sidebar (QGIS-style tree) --}}
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

    {{-- Layer Tree (drag-reorderable with nested polygons) --}}
    <div class="flex-1 overflow-y-auto" id="layer-list-container">
        <div class="px-1 py-2">
            <template x-for="(layer, index) in layers" :key="layer.id">
                <div class="tree-layer-group mb-0.5 mx-1">
                    {{-- Layer parent node --}}
                    <div class="layer-item rounded-lg px-3 py-2.5 cursor-pointer group hover:shadow"
                        :class="{
                            'active': activeLayer?.id === layer.id,
                            'tree-expanded': layer._expanded
                        }"
                        draggable="true"
                        @dragstart="onDragStart($event, index)"
                        @dragover.prevent="onDragOver($event, index)"
                        @dragleave="onDragLeave($event)"
                        @drop="onDrop($event, index)"
                        @dragend="onDragEnd($event)"
                        @click="selectLayer(layer)">

                        <div class="flex items-center gap-2">
                            {{-- Tree expand/collapse toggle --}}
                            <button class="flex-shrink-0 tree-toggle-btn"
                                @click.stop="toggleTreeExpand(layer)"
                                :title="layer._expanded ? 'Ciutkan' : 'Perluas'">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform duration-150"
                                    :class="{ 'rotate-90': layer._expanded }"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

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

                            {{-- Layer name --}}
                            <div class="flex-1 min-w-0">
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

                    {{-- Polygon sub-tree (nested children) --}}
                    <div x-show="layer._expanded"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="tree-children">
                        <template x-for="(poly, idx) in getLayerPolygons(layer)" :key="poly.id || ('new-' + idx)">
                            <div class="polygon-tree-item flex items-center gap-2 pl-9 pr-3 py-1.5 cursor-pointer rounded-md"
                                :class="{ 'polygon-editing': editingPolygon?.id === poly.id && editingPolygon?.layerId === layer.id }"
                                @click.stop="selectPolygonForEdit(layer, poly, idx)">

                                {{-- Tree branch connector --}}
                                <div class="tree-connector flex-shrink-0">
                                    <svg class="h-3 w-3 text-base-content/20" viewBox="0 0 12 12">
                                        <path d="M0 0 L0 6 L12 6" fill="none" stroke="currentColor" stroke-width="1.5"/>
                                    </svg>
                                </div>

                                {{-- Polygon color swatch --}}
                                <div class="w-3 h-3 rounded-sm flex-shrink-0 border border-black/10"
                                    :style="'background-color:' + (layer.warna || '#6b7280')"></div>

                                {{-- Polygon name (inline edit) --}}
                                <input type="text" class="input input-bordered input-xs flex-1 min-w-0 bg-transparent"
                                    :value="poly.nama || 'Polygon ' + (idx + 1)"
                                    @change="updatePolygonName(poly, $event.target.value)"
                                    @click.stop
                                    placeholder="Nama polygon...">

                                {{-- Zoom to polygon --}}
                                <button class="btn btn-ghost btn-xs btn-circle opacity-50 hover:opacity-100"
                                    @click.stop="zoomToPolygon(poly)" title="Zoom ke polygon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>

                                {{-- Delete polygon --}}
                                <button class="btn btn-ghost btn-xs btn-circle text-error opacity-50 hover:opacity-100"
                                    @click.stop="deletePolygon(poly, idx)" title="Hapus polygon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </template>

                        {{-- Empty polygon state --}}
                        <template x-if="getLayerPolygons(layer).length === 0">
                            <p class="text-[11px] text-base-content/40 text-center py-2 pl-9">
                                Belum ada polygon. Klik layer lalu gambar di peta.
                            </p>
                        </template>
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

    {{-- Editing indicator --}}
    <template x-if="editingPolygon">
        <div class="px-3 py-2 border-t border-base-200 flex-shrink-0 bg-primary/5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-primary animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    <span class="font-medium text-primary">Editing:</span>
                    <span class="truncate" x-text="editingPolygon.nama || 'Polygon'"></span>
                </div>
                <button class="btn btn-ghost btn-xs" @click="stopEditingPolygon()" title="Selesai edit">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Selesai
                </button>
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
