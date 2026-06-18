USE ClothingStore;

UPDATE tblClothes
SET image_path = 'assets/images/products/floral-dress.jpg'
WHERE title = 'Floral Summer Dress';

UPDATE tblClothes
SET image_path = 'assets/images/products/denim-jacket.jpg'
WHERE title = 'Vintage Denim Jacket';

UPDATE tblClothes
SET image_path = 'assets/images/products/silk-blouse.jpg'
WHERE title = 'Silk Blouse';

UPDATE tblClothes
SET image_path = 'assets/images/products/fallback-product.jpg'
WHERE title = "Men's Oxford Shirt";

UPDATE tblClothes
SET image_path = 'assets/images/products/running-shoes.jpg'
WHERE title = 'Chelsea Boots';

INSERT INTO tblClothes (seller_id, title, brand, category, gender, size_label, condition_rating, sell_price, description, image_path, status)
SELECT 3, 'Tailored Wool Coat', 'Mango', 'Outerwear', 'Women', 'M', 5, 1200.00, 'Warm tailored coat with a polished pre-loved finish.', 'assets/images/products/wool-coat.jpg', 'approved'
WHERE NOT EXISTS (
    SELECT 1 FROM tblClothes WHERE title = 'Tailored Wool Coat'
);

INSERT INTO tblClothes (seller_id, title, brand, category, gender, size_label, condition_rating, sell_price, description, image_path, status)
SELECT 3, 'Structured Leather Bag', 'Topshop', 'Accessories', 'Women', 'One Size', 4, 540.00, 'Compact leather-look bag with clean hardware and everyday space.', 'assets/images/products/leather-bag.jpg', 'approved'
WHERE NOT EXISTS (
    SELECT 1 FROM tblClothes WHERE title = 'Structured Leather Bag'
);
