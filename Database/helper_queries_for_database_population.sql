INSERT INTO categories (category)
VALUES ("SZÉPSÉGÁPOLÁS");
INSERT INTO categories (category)
VALUES ("HÁZTARTÁS");
INSERT INTO categories (category)
VALUES ("BIO TERMÉKEK");
INSERT INTO categories (category)
VALUES ("ÉTREND-KIEGÉSZÍTŐK");

-- reset id counter

ALTER TABLE `brands` AUTO_INCREMENT=1;

-- maybe it would be a good idea to reset the seed by deletion to the (deletedId -1) 