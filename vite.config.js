import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/css/guest.css",
                "resources/js/guest/app.js",
                "resources/css/peta.css",
                "resources/js/map/index.js",
                "resources/js/guest/kelurahan-map.js",
                "resources/js/peta/app.js",
                "resources/js/peta/layer-form.js",
                "resources/js/peta/layer-polygon-editor.js",
                "resources/js/peta/rw-polygon-editor.js",
                "resources/js/peta/layer-manager.js",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
});
