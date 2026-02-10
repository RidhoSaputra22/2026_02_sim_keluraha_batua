#!/bin/bash

# Fix all factories with sed replacements

cd "/home/codeslayer/Desktop/PROJECT LARAVEL/2026_02_10_sim_kelurahan_batua/2026_02_sim_keluraha_batua/database/factories"

# Fix double semicolons globally
find . -name "*Factory.php" -exec sed -i 's/];;/];/g' {} \;

# Fix petugas_input globally - words to numberBetween
find . -name "*Factory.php" -exec sed -i "s/'petugas_input' => \$this->faker->words(3, true)/'petugas_input' => \$this->faker->numberBetween(1, 10)/g" {} \;

# Fix tgl_input globally - date() to dateTimeBetween()
find . -name "*Factory.php" -exec sed -i "s/'tgl_input' => \$this->faker->date()/'tgl_input' => \$this->faker->dateTimeBetween('-2 years', 'now')/g" {} \;

# Fix phoneNumber() to numerify()
find . -name "*Factory.php" -exec sed -i "s/->phoneNumber()/->numerify('08##########')/g" {} \;

# Fix RT/RW fields - words to padded numbers
find . -name "*Factory.php" -exec sed -i "s/'rt' => \$this->faker->words(3, true)/'rt' => str_pad(\$this->faker->numberBetween(1, 20), 3, '0', STR_PAD_LEFT)/g" {} \;
find . -name "*Factory.php" -exec sed -i "s/'rw' => \$this->faker->words(3, true)/'rw' => str_pad(\$this->faker->numberBetween(1, 10), 3, '0', STR_PAD_LEFT)/g" {} \;

echo "✅ Global fixes applied!"
echo "📝 Fixed: double semicolons, petugas_input, tgl_input, phoneNumber, rt/rw"
