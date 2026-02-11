#!/bin/bash

# Script to complete master data features (Jenis Surat, Template Surat, Referensi)
cd "/home/codeslayer/Desktop/PROJECT LARAVEL/2026_02_10_sim_kelurahan_batua/2026_02_sim_keluraha_batua"

echo "Running migrations..."
php artisan migrate

echo "Creating views directories..."
mkdir -p resources/views/admin/jenis-surat
mkdir -p resources/views/admin/template-surat
mkdir -p resources/views/admin/referensi

echo "Done! Now update models, controllers, and create views manually."
