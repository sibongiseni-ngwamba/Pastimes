SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `tblUser` ENGINE=InnoDB;
ALTER TABLE `tblAdmin` ENGINE=InnoDB;
ALTER TABLE `tblAddress` ENGINE=InnoDB;
ALTER TABLE `tblSellerApplication` ENGINE=InnoDB;
ALTER TABLE `tblClothes` ENGINE=InnoDB;
ALTER TABLE `tblCartItem` ENGINE=InnoDB;
ALTER TABLE `tblOrder` ENGINE=InnoDB;
ALTER TABLE `tblOrderItem` ENGINE=InnoDB;
ALTER TABLE `tblMessage` ENGINE=InnoDB;

ALTER TABLE `tblUser`
    ADD COLUMN IF NOT EXISTS `profile_image_path` VARCHAR(255) DEFAULT NULL AFTER `phone_number`;

ALTER TABLE `tblClothes`
    ADD COLUMN IF NOT EXISTS `inventory_quantity` INT NOT NULL DEFAULT 1 AFTER `image_path`;

ALTER TABLE `tblOrder`
    ADD COLUMN IF NOT EXISTS `order_reference` VARCHAR(20) DEFAULT NULL AFTER `order_total`,
    ADD COLUMN IF NOT EXISTS `session_reference` VARCHAR(20) DEFAULT NULL AFTER `order_reference`;

ALTER TABLE `tblMessage`
    ADD COLUMN IF NOT EXISTS `related_order_id` INT DEFAULT NULL AFTER `receiver_user_id`;

ALTER TABLE `tblMessage`
    ADD COLUMN IF NOT EXISTS `receiver_admin_id` INT DEFAULT NULL AFTER `receiver_user_id`;

CREATE TABLE IF NOT EXISTS `tblProductImage` (
    `image_id` INT NOT NULL AUTO_INCREMENT,
    `clothes_id` INT NOT NULL,
    `image_path` VARCHAR(255) NOT NULL,
    `sort_order` INT NOT NULL DEFAULT 1,
    `alt_text` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`image_id`),
    UNIQUE KEY `ux_product_image_sort` (`clothes_id`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `tblProductImage` (`clothes_id`, `image_path`, `sort_order`, `alt_text`)
SELECT `clothes_id`, `image_path`, 1, `title`
FROM `tblClothes`;

UPDATE `tblClothes`
SET `inventory_quantity` = CASE WHEN `status` = 'sold' THEN 0 ELSE 1 END;

CREATE OR REPLACE VIEW `users` AS
SELECT `user_id`, `full_name`, `first_name`, `last_name`, `username`, `email`, `password_hash`, `phone_number`, `profile_image_path`, `role`, `customer_status`, `seller_status`, `is_active`, `created_at`, `updated_at`
FROM `tblUser`;

CREATE OR REPLACE VIEW `addresses` AS
SELECT `address_id`, `user_id`, `address_label`, `street_address`, `suburb`, `city`, `province`, `postal_code`, `is_default`, `created_at`
FROM `tblAddress`;

CREATE OR REPLACE VIEW `seller_applications` AS
SELECT `application_id`, `user_id`, `id_number`, `motivation`, `status`, `admin_id`, `created_at`, `reviewed_at`
FROM `tblSellerApplication`;

CREATE OR REPLACE VIEW `pending_listings` AS
SELECT `clothes_id`, `seller_id`, `title`, `brand`, `category`, `gender`, `size_label`, `condition_rating`, `sell_price`, `description`, `image_path`, `inventory_quantity`, `status`, `created_at`, `updated_at`
FROM `tblClothes`
WHERE `status` = 'pending';

CREATE OR REPLACE VIEW `products` AS
SELECT `clothes_id` AS `product_id`, `seller_id`, `title`, `brand`, `category`, `gender`, `size_label`, `condition_rating`, `sell_price`, `description`, `image_path`, `inventory_quantity`, `status`, `created_at`, `updated_at`
FROM `tblClothes`;

CREATE OR REPLACE VIEW `product_images` AS
SELECT `image_id`, `clothes_id` AS `product_id`, `image_path`, `sort_order`, `alt_text`, `created_at`
FROM `tblProductImage`;

CREATE OR REPLACE VIEW `cart_items` AS
SELECT `cart_id`, `user_id`, `clothes_id` AS `product_id`, `quantity`, `created_at`
FROM `tblCartItem`;

CREATE OR REPLACE VIEW `orders` AS
SELECT `order_id`, `user_id`, `address_id`, `order_total`, `order_reference`, `session_reference`, `status`, `created_at`, `updated_at`
FROM `tblOrder`;

CREATE OR REPLACE VIEW `order_items` AS
SELECT `item_id`, `order_id`, `clothes_id` AS `product_id`, `quantity`, `price_each`
FROM `tblOrderItem`;

CREATE OR REPLACE VIEW `messages` AS
SELECT `message_id`, `sender_user_id`, `sender_admin_id`, `receiver_user_id`, `receiver_admin_id`, `related_order_id`, `title`, `message_body`, `is_broadcast`, `is_read`, `created_at`
FROM `tblMessage`;

ALTER TABLE `tblAddress`
    ADD CONSTRAINT `fk_address_user` FOREIGN KEY (`user_id`) REFERENCES `tblUser` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `tblSellerApplication`
    ADD CONSTRAINT `fk_application_user` FOREIGN KEY (`user_id`) REFERENCES `tblUser` (`user_id`) ON DELETE CASCADE,
    ADD CONSTRAINT `fk_application_admin` FOREIGN KEY (`admin_id`) REFERENCES `tblAdmin` (`admin_id`) ON DELETE SET NULL;

ALTER TABLE `tblClothes`
    ADD CONSTRAINT `fk_clothes_seller` FOREIGN KEY (`seller_id`) REFERENCES `tblUser` (`user_id`) ON DELETE SET NULL;

ALTER TABLE `tblProductImage`
    ADD CONSTRAINT `fk_product_image_clothes` FOREIGN KEY (`clothes_id`) REFERENCES `tblClothes` (`clothes_id`) ON DELETE CASCADE;

ALTER TABLE `tblCartItem`
    ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `tblUser` (`user_id`) ON DELETE CASCADE,
    ADD CONSTRAINT `fk_cart_clothes` FOREIGN KEY (`clothes_id`) REFERENCES `tblClothes` (`clothes_id`) ON DELETE CASCADE;

ALTER TABLE `tblOrder`
    ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `tblUser` (`user_id`) ON DELETE CASCADE,
    ADD CONSTRAINT `fk_order_address` FOREIGN KEY (`address_id`) REFERENCES `tblAddress` (`address_id`) ON DELETE RESTRICT;

ALTER TABLE `tblOrderItem`
    ADD CONSTRAINT `fk_order_item_order` FOREIGN KEY (`order_id`) REFERENCES `tblOrder` (`order_id`) ON DELETE CASCADE,
    ADD CONSTRAINT `fk_order_item_clothes` FOREIGN KEY (`clothes_id`) REFERENCES `tblClothes` (`clothes_id`) ON DELETE RESTRICT;

ALTER TABLE `tblMessage`
    ADD CONSTRAINT `fk_message_sender_user` FOREIGN KEY (`sender_user_id`) REFERENCES `tblUser` (`user_id`) ON DELETE SET NULL,
    ADD CONSTRAINT `fk_message_sender_admin` FOREIGN KEY (`sender_admin_id`) REFERENCES `tblAdmin` (`admin_id`) ON DELETE SET NULL,
    ADD CONSTRAINT `fk_message_receiver_user` FOREIGN KEY (`receiver_user_id`) REFERENCES `tblUser` (`user_id`) ON DELETE SET NULL,
    ADD CONSTRAINT `fk_message_receiver_admin` FOREIGN KEY (`receiver_admin_id`) REFERENCES `tblAdmin` (`admin_id`) ON DELETE SET NULL,
    ADD CONSTRAINT `fk_message_related_order` FOREIGN KEY (`related_order_id`) REFERENCES `tblOrder` (`order_id`) ON DELETE SET NULL;

SET FOREIGN_KEY_CHECKS = 1;
