-- Az "idegen kulcsok ellenőrzésének engedélyezése" opcióról a pipát ki kell venni phpMyAdminban

-- Inaktíváljuk a foreign key constraint-eket
SET FOREIGN_KEY_CHECKS=0;

-- Táblák ürítése
TRUNCATE TABLE categories_to_brands;
TRUNCATE TABLE categories_to_ingredients;
TRUNCATE TABLE categories_to_products;
TRUNCATE TABLE ingredients;
TRUNCATE TABLE ingredients_to_ingredient_functions;
TRUNCATE TABLE ingredient_functions;
TRUNCATE TABLE products;
TRUNCATE TABLE products_to_ingredients;
TRUNCATE TABLE products_to_subcategories;
TRUNCATE TABLE ratings;

-- Aktiváljuk a foreign key constraint-eket
SET FOREIGN_KEY_CHECKS=1;