@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const colors = {
        primary:   '#6366f1',
        secondary: '#ec4899',
        accent:    '#06b6d4',
        success:   '#22c55e',
        warning:   '#f59e0b',
        error:     '#ef4444',
        info:      '#3b82f6',
    };
    const palette = ['#6366f1','#ec4899','#06b6d4','#22c55e','#f59e0b','#ef4444','#3b82f6','#8b5cf6','#f97316','#14b8a6','#e11d48','#0ea5e9'];

    Chart.defaults.font.family = 'inherit';
    Chart.defaults.font.size = 12;
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.pointStyleWidth = 10;
    Chart.defaults.plugins.legend.labels.padding = 12;

    const mutasiTrend = @json($mutasiTrend);
    new Chart(document.getElementById('chartMutasiTrend'), {
        type: 'line',
        data: {
            labels: mutasiTrend.map(m => m.label),
            datasets: [
                { label: 'Lahir',     data: mutasiTrend.map(m => m.lahir),     borderColor: colors.success, backgroundColor: colors.success+'22', tension: 0.3, fill: true, pointRadius: 3 },
                { label: 'Meninggal', data: mutasiTrend.map(m => m.meninggal), borderColor: colors.error,   backgroundColor: colors.error+'22',   tension: 0.3, fill: true, pointRadius: 3 },
                { label: 'Datang',    data: mutasiTrend.map(m => m.datang),    borderColor: colors.info,    backgroundColor: colors.info+'22',    tension: 0.3, fill: true, pointRadius: 3 },
                { label: 'Pindah',    data: mutasiTrend.map(m => m.pindah),    borderColor: colors.warning, backgroundColor: colors.warning+'22', tension: 0.3, fill: true, pointRadius: 3 },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#e5e7eb44' } },
                x: { grid: { display: false } }
            },
            plugins: { legend: { position: 'bottom' } }
        }
    });

    const pendudukPerRw = @json($pendudukPerRw);
    new Chart(document.getElementById('chartPendudukPerRw'), {
        type: 'bar',
        data: {
            labels: pendudukPerRw.map(r => 'RW ' + String(r.nomor).padStart(2, '0')),
            datasets: [{
                label: 'Penduduk',
                data: pendudukPerRw.map(r => r.total_penduduk),
                backgroundColor: palette.slice(0, pendudukPerRw.length).map(c => c + '88'),
                borderColor: palette.slice(0, pendudukPerRw.length),
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#e5e7eb44' } },
                x: { grid: { display: false } }
            },
            plugins: { legend: { display: false } }
        }
    });

    const genderData = @json($genderData);
    new Chart(document.getElementById('chartGender'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(genderData).map(k => k.charAt(0).toUpperCase() + k.slice(1)),
            datasets: [{
                data: Object.values(genderData),
                backgroundColor: [colors.primary, colors.secondary, '#94a3b8'],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: { legend: { display: false } }
        }
    });

    const agamaData = @json($agamaData);
    new Chart(document.getElementById('chartAgama'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(agamaData).map(k => k.charAt(0).toUpperCase() + k.slice(1)),
            datasets: [{
                data: Object.values(agamaData),
                backgroundColor: palette.slice(0, Object.keys(agamaData).length),
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: { legend: { display: false } }
        }
    });

    const golDarahData = @json($golDarahData);
    new Chart(document.getElementById('chartGolDarah'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(golDarahData).map(k => k.toUpperCase()),
            datasets: [{
                data: Object.values(golDarahData),
                backgroundColor: [colors.error, colors.info, colors.warning, colors.success, '#94a3b8'],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: { legend: { display: false } }
        }
    });

    const statusKawinData = @json($statusKawinData);
    new Chart(document.getElementById('chartStatusKawin'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusKawinData).map(k => k.replace(/_/g,' ').replace(/\b\w/g, l => l.toUpperCase())),
            datasets: [{
                data: Object.values(statusKawinData),
                backgroundColor: [colors.primary, colors.secondary, colors.accent, colors.warning, '#94a3b8'],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: { legend: { display: false } }
        }
    });

    const pendidikanData = @json($pendidikanData);
    new Chart(document.getElementById('chartPendidikan'), {
        type: 'bar',
        data: {
            labels: Object.keys(pendidikanData).map(k => k.replace(/_/g,' ').toUpperCase()),
            datasets: [{
                label: 'Jumlah',
                data: Object.values(pendidikanData),
                backgroundColor: palette.slice(0, Object.keys(pendidikanData).length).map(c => c + '88'),
                borderColor: palette.slice(0, Object.keys(pendidikanData).length),
                borderWidth: 1,
                borderRadius: 4,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#e5e7eb44' } },
                y: { grid: { display: false } }
            },
            plugins: { legend: { display: false } }
        }
    });

    const umkmPerRw = @json($umkmPerRw);
    new Chart(document.getElementById('chartUmkmPerRw'), {
        type: 'bar',
        data: {
            labels: umkmPerRw.map(r => 'RW ' + String(r.nomor).padStart(2, '0')),
            datasets: [{
                label: 'UMKM',
                data: umkmPerRw.map(r => r.total_umkm),
                backgroundColor: colors.info + '88',
                borderColor: colors.info,
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#e5e7eb44' } },
                x: { grid: { display: false } }
            },
            plugins: { legend: { display: false } }
        }
    });

    const kkPerRw = @json($kkPerRw);
    new Chart(document.getElementById('chartKkPerRw'), {
        type: 'bar',
        data: {
            labels: kkPerRw.map(r => 'RW ' + String(r.nomor).padStart(2, '0')),
            datasets: [{
                label: 'KK',
                data: kkPerRw.map(r => r.total_kk),
                backgroundColor: colors.secondary + '88',
                borderColor: colors.secondary,
                borderWidth: 1,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#e5e7eb44' } },
                x: { grid: { display: false } }
            },
            plugins: { legend: { display: false } }
        }
    });

    const map = L.map('dashboard-map', {
        center: [-5.1575, 119.475],
        zoom: 14,
        zoomControl: true,
        attributionControl: false,
        scrollWheelZoom: true,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    fetch('{{ route("peta.geojson.rw") }}')
        .then(res => res.json())
        .then(data => {
            if (data && data.features) {
                L.geoJSON(data, {
                    style: function(feature) {
                        const color = feature.properties?.warna || palette[feature.properties?.nomor % palette.length] || colors.primary;
                        return {
                            color: color,
                            weight: 2,
                            opacity: 0.8,
                            fillColor: color,
                            fillOpacity: 0.25,
                        };
                    },
                    onEachFeature: function(feature, layer) {
                        const props = feature.properties || {};
                        const rwData = pendudukPerRw.find(r => r.nomor == props.nomor);
                        const kkData = kkPerRw.find(r => r.nomor == props.nomor);
                        const umkmData = umkmPerRw.find(r => r.nomor == props.nomor);

                        const popupHtml = '<div class="text-sm">' +
                            '<strong class="text-base">RW ' + String(props.nomor || '').padStart(2,'0') + '</strong>' +
                            '<hr class="my-1 border-base-300">' +
                            '<div class="space-y-1">' +
                            '<div>Penduduk: <strong>' + (rwData?.total_penduduk || 0) + '</strong> jiwa</div>' +
                            '<div>KK: <strong>' + (kkData?.total_kk || 0) + '</strong></div>' +
                            '<div>UMKM: <strong>' + (umkmData?.total_umkm || 0) + '</strong></div>' +
                            '</div></div>';
                        layer.bindPopup(popupHtml);

                        const center = layer.getBounds().getCenter();
                        L.marker(center, {
                            icon: L.divIcon({
                                className: 'rw-label',
                                html: '<div style="font-size:11px;font-weight:700;color:' + (props.warna || '#333') + ';text-shadow:0 0 4px #fff,0 0 4px #fff;">RW ' + String(props.nomor || '').padStart(2,'0') + '</div>',
                                iconSize: [50, 20],
                                iconAnchor: [25, 10]
                            })
                        }).addTo(map);
                    }
                }).addTo(map);
            }
        })
        .catch(err => console.warn('Could not load RW GeoJSON:', err));

    setTimeout(() => map.invalidateSize(), 300);
});
</script>
@endpush
