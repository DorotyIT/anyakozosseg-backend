INSERT INTO categories (category)
VALUES ("SZÉPSÉGÁPOLÁS");
INSERT INTO categories (category)
VALUES ("HÁZTARTÁS");
INSERT INTO categories (category)
VALUES ("BIO TERMÉKEK");
INSERT INTO categories (category)
VALUES ("ÉTREND-KIEGÉSZÍTŐK");

-- reset id counter

set foreign_key_checks=0;
truncate table brands;
set foreign_key_checks=1;

-- maybe it would be a good idea to reset the seed by deletion to the (deletedId -1) 