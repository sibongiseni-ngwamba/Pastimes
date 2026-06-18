# PASTIMES - Pre-Loved Fashion Marketplace

Pastimes is a South African pre-loved fashion marketplace for buying and selling second-hand branded clothing.

## Main Roles

- Guest
- Buyer / Customer
- Verified Seller
- Administrator

## What This Build Includes

- Bcrypt registration and login flow with a shared customer/admin login page
- Session-based shopping cart with quantity updates and persistence
- Checkout that writes orders and order items, updates inventory, and generates order/session references
- Buyer purchase history report with a grand total
- Seller verification workflow with admin approval
- Seller listing upload, edit, and sold-items view
- Admin user, listing, order, and messaging management
- Multiple delivery addresses with default-address support
- User profile editing with profile picture uploads
- Internal messaging and admin broadcast messages
- Mobile-first responsive styling

## Setup

1. Configure your MySQL credentials in environment variables or in `DBConn.php`.
2. Import the schema:
   - Run `loadClothingStore.php` to create the base tables, seed data, and apply the Pastimes schema migration.
   - Or run `database/pastimes_schema_updates.sql` manually after importing `database/myClothingStore.sql`.
3. Open `index.php` in your PHP server.

## Demo Accounts

- Customer: `jdoe` / `12345678`
- Pending customer: `ayanda` / `12345678`
- Approved seller: `lebo.k` / `12345678`
- Admin: `admin@pastimes.co.za` / `12345678`

## Important Notes

- New user registrations use `password_hash()` with BCRYPT.
- The app keeps a compatibility fallback for legacy demo hashes in the seeded data.
- The main login page lets you sign in as either a customer or an administrator.
- Cart items are stored in session and also synced to the cart table when a user is logged in.
- Product image uploads are saved under `assets/images/uploads/`.
- Profile pictures are stored in `assets/images/uploads/` and shown in the header after upload.
