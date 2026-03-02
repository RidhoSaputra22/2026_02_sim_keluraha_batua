import { defineConfig } from "vitest/config";

export default defineConfig({
    test: {
        environment: "jsdom",
        globals: true,
        setupFiles: ["./resources/js/map/__tests__/setup.js"],
        include: ["resources/js/map/__tests__/**/*.{test,spec}.js"],
    },
});
