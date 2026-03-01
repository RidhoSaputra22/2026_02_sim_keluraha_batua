/**
 * SIM Kelurahan Batua — Map Engine
 *
 * Barrel export that registers `window.SimPeta` so Alpine.js
 * components in Blade templates can access the engine classes.
 *
 * Architecture:
 *   resources/js/map/
 *   ├── index.js                    ← you are here
 *   ├── MapEngine.js                ← core map init + base layers
 *   ├── layers/
 *   │   ├── KelurahanLayer.js       ← boundary line
 *   │   ├── RwLayer.js              ← RW polygons, colors, interaction
 *   │   └── CustomLayerManager.js   ← custom overlay layers
 *   ├── renderers/
 *   │   └── PatternRenderer.js      ← SVG hatch/dots/crosshatch
 *   ├── editors/
 *   │   └── PolygonEditor.js        ← Leaflet.Draw wrapper
 *   └── utils/
 *       ├── ApiClient.js            ← fetch with CSRF
 *       └── helpers.js              ← formatNumber, etc.
 *
 * Usage in Blade:
 *   const engine = new SimPeta.MapEngine('map').init();
 *   const rw = new SimPeta.RwLayer(engine, { onSelect: … });
 *   await rw.load('/peta/geojson/rw');
 *
 * @module map
 */

import MapEngine from "./MapEngine";
import RwLayer from "./layers/RwLayer";
import KelurahanLayer from "./layers/KelurahanLayer";
import CustomLayerManager from "./layers/CustomLayerManager";
import PatternRenderer from "./renderers/PatternRenderer";
import PolygonEditor from "./editors/PolygonEditor";
import { formatNumber, sortRwList, getCsrfToken } from "./utils/helpers";
import { apiGet, apiPut, apiPost, apiDelete } from "./utils/ApiClient";

// Named exports for ES module consumers
export {
    MapEngine,
    RwLayer,
    KelurahanLayer,
    CustomLayerManager,
    PatternRenderer,
    PolygonEditor,
    formatNumber,
    sortRwList,
    getCsrfToken,
    apiGet,
    apiPut,
    apiPost,
    apiDelete,
};

// Register on window for inline <script> / Alpine.js access
window.SimPeta = {
    MapEngine,
    RwLayer,
    KelurahanLayer,
    CustomLayerManager,
    PatternRenderer,
    PolygonEditor,
    formatNumber,
    sortRwList,
    getCsrfToken,
    apiGet,
    apiPut,
    apiPost,
    apiDelete,
};
