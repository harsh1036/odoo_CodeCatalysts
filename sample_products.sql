-- Sample product data for testing the product details page
-- Run this after your database schema is set up

USE renewable_cloth;

-- Insert sample products with images and tags
INSERT INTO items (item_id, owner_id, title, description, category, size, `condition`, tags, images, status, point_value, created_at) VALUES
(
    UUID(),
    (SELECT user_id FROM users WHERE email = 'john@example.com' LIMIT 1),
    'Vintage Denim Jacket',
    'Classic blue denim jacket in excellent condition. Perfect for layering in any season. Features a comfortable fit and timeless style.',
    'Jacket',
    'M',
    'Good',
    '["vintage", "denim", "casual", "classic"]',
    '["https://images.unsplash.com/photo-1576871337622-98d48d1cf531?w=400", "https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=400"]',
    'Available',
    150,
    NOW()
),
(
    UUID(),
    (SELECT user_id FROM users WHERE email = 'jane@example.com' LIMIT 1),
    'Organic Cotton T-Shirt',
    'Soft, breathable organic cotton t-shirt. Perfect for everyday wear. Available in a neutral color that goes with everything.',
    'Shirt',
    'L',
    'Like New',
    '["organic", "cotton", "casual", "sustainable"]',
    '["https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=400", "https://images.unsplash.com/photo-1503341504253-dff4815485f1?w=400"]',
    'Available',
    80,
    NOW()
),
(
    UUID(),
    (SELECT user_id FROM users WHERE email = 'john@example.com' LIMIT 1),
    'Leather Crossbody Bag',
    'Genuine leather crossbody bag with adjustable strap. Multiple compartments for organization. Perfect for daily use.',
    'Accessories',
    'One Size',
    'Good',
    '["leather", "crossbody", "practical", "stylish"]',
    '["https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=400", "https://images.unsplash.com/photo-1590874103328-eac38a683ce7?w=400"]',
    'Available',
    200,
    NOW()
),
(
    UUID(),
    (SELECT user_id FROM users WHERE email = 'jane@example.com' LIMIT 1),
    'High-Waisted Jeans',
    'Comfortable high-waisted jeans with a modern fit. Stretchy denim material that looks great and feels even better.',
    'Pants',
    'S',
    'New',
    '["high-waisted", "stretchy", "modern", "comfortable"]',
    '["https://images.unsplash.com/photo-1542272604-787c3835535d?w=400", "https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=400"]',
    'Available',
    120,
    NOW()
),
(
    UUID(),
    (SELECT user_id FROM users WHERE email = 'john@example.com' LIMIT 1),
    'Wool Sweater',
    'Cozy wool sweater perfect for cold weather. Soft texture and comfortable fit. Available in a versatile neutral color.',
    'Jacket',
    'XL',
    'Like New',
    '["wool", "warm", "cozy", "winter"]',
    '["https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=400", "https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400"]',
    'Available',
    180,
    NOW()
),
(
    UUID(),
    (SELECT user_id FROM users WHERE email = 'jane@example.com' LIMIT 1),
    'Summer Dress',
    'Light and flowy summer dress perfect for warm weather. Features a flattering silhouette and comfortable fabric.',
    'Dress',
    'M',
    'Good',
    '["summer", "flowy", "comfortable", "casual"]',
    '["https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=400", "https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=400"]',
    'Available',
    100,
    NOW()
),
(
    UUID(),
    (SELECT user_id FROM users WHERE email = 'john@example.com' LIMIT 1),
    'Canvas Sneakers',
    'Comfortable canvas sneakers perfect for everyday wear. Durable construction and classic design.',
    'Shoes',
    '42',
    'Good',
    '["canvas", "sneakers", "casual", "comfortable"]',
    '["https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400", "https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=400"]',
    'Available',
    90,
    NOW()
),
(
    UUID(),
    (SELECT user_id FROM users WHERE email = 'jane@example.com' LIMIT 1),
    'Silk Scarf',
    'Elegant silk scarf with beautiful pattern. Perfect accessory for any outfit. Lightweight and versatile.',
    'Accessories',
    'One Size',
    'New',
    '["silk", "elegant", "versatile", "accessory"]',
    '["https://images.unsplash.com/photo-1520903920243-00d872a2d1c9?w=400", "https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=400"]',
    'Available',
    60,
    NOW()
);

-- Verify the data was inserted
SELECT 
    item_id,
    title,
    category,
    size,
    `condition`,
    point_value,
    status,
    created_at
FROM items 
WHERE status = 'Available' 
ORDER BY created_at DESC; 