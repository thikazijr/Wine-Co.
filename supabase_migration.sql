-- ==============================================================================
-- WINE & CO. ESWATINI - SUPABASE POSTGRESQL SCHEMA & INITIAL DATA MIGRATION
-- ==============================================================================

-- Enable UUID extension
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- ------------------------------------------------------------------------------
-- 1. PROFILES & STAFF (Auth-linked user profiles)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS public.profiles (
    id UUID PRIMARY KEY REFERENCES auth.users(id) ON DELETE CASCADE,
    email TEXT UNIQUE NOT NULL,
    full_name TEXT,
    phone TEXT,
    address TEXT,
    city TEXT,
    role TEXT DEFAULT 'customer' CHECK (role IN ('customer', 'staff', 'manager', 'admin')),
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Staff table for legacy/direct management if needed
CREATE TABLE IF NOT EXISTS public.staff (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    role TEXT DEFAULT 'staff' CHECK (role IN ('admin', 'manager', 'staff')),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    last_login TIMESTAMPTZ
);

INSERT INTO public.staff (name, email, role, is_active) VALUES
('Administrator', 'admin@wineco.co.sz', 'admin', TRUE),
('Siphiwo Sethu Thikazi', 'siphiwosethuthikazi@gmail.com', 'admin', TRUE),
('Phumelele Dlamini', 'phumelele@wineco.co.sz', 'manager', TRUE),
('Lihle Mbhamali', 'lihle@wineco.co.sz', 'manager', TRUE)
ON CONFLICT (email) DO NOTHING;

-- ------------------------------------------------------------------------------
-- 2. WINES CATALOG
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS public.wines (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name TEXT NOT NULL,
    variety TEXT NOT NULL,
    origin TEXT NOT NULL,
    structure TEXT,
    taste TEXT,
    strength TEXT,
    vintage INT,
    price NUMERIC(10,2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    description TEXT,
    featured BOOLEAN DEFAULT FALSE,
    in_stock BOOLEAN DEFAULT TRUE,
    image_url TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

INSERT INTO public.wines (id, name, variety, origin, structure, taste, strength, vintage, price, stock_quantity, description, featured, in_stock, image_url) OVERRIDING SYSTEM VALUE VALUES
(1, 'Château Margaux', 'Cabernet Sauvignon', 'Bordeaux, France', 'Full-bodied', 'Elegant, blackcurrant, cedar, tobacco', '14.5%', 2018, 571.00, 24, 'A legendary Bordeaux with incredible depth and aging potential.', TRUE, TRUE, '/wines/margaux.jpg'),
(2, 'Cloudy Bay', 'Sauvignon Blanc', 'Marlborough, New Zealand', 'Crisp', 'Citrus, passionfruit, grassy, gooseberry', '13.5%', 2021, 615.00, 21, 'New Zealand''s iconic Sauvignon Blanc. Vibrant and aromatic.', TRUE, TRUE, '/wines/cloudybay.jpg'),
(3, 'Kanonkop Pinotage', 'Pinotage', 'Stellenbosch, South Africa', 'Rich', 'Plum, coffee, chocolate, blackberry', '14.5%', 2020, 345.00, 16, 'South Africa''s flagship Pinotage. Bold and characterful.', TRUE, TRUE, '/wines/kanonkop.jpg'),
(4, 'Opus One', 'Cabernet Sauvignon', 'Napa Valley, USA', 'Powerful', 'Dark fruit, vanilla, oak, cassis', '14.8%', 2018, 745.00, 14, 'Napa Valley masterpiece from Robert Mondavi and Baron Philippe.', TRUE, TRUE, '/wines/opusone.jpg'),
(5, 'Kanonkop Kadette', 'Cape Blend', 'Stellenbosch, South Africa', 'Medium', 'Red berry, spice, vanilla, plum', '14%', 2021, 499.00, 20, 'Excellent entry-level Cape Blend. Perfect for everyday enjoyment.', FALSE, TRUE, '/wines/kanonkop-kadette.jpg'),
(6, 'Meerlust Rubicon', 'Bordeaux Blend', 'Stellenbosch, South Africa', 'Complex', 'Black fruit, cedar, spice, leather', '14.2%', 2018, 273.00, 17, 'South Africa''s iconic Bordeaux-style blend. Aged in French oak.', FALSE, TRUE, '/wines/meerlust.jpg'),
(7, 'La Torre', 'Montepulciano d''Abruzzo', 'Italy (Abruzzo)', 'Full-bodied', 'Dark cherry, plum, earthy, with firm tannins', '14% - 15%', 2020, 980.00, 20, 'A classic Montepulciano d''Abruzzo from Tenuta Pescarnia. This 2020 vintage offers rich, dark fruit flavours with a rustic, earthy character and robust structure, reflecting the terroir of the Abruzzo region.', FALSE, TRUE, '/wines/la-torre.jpg'),
(8, 'The Reserve', 'Red Blend', 'South Africa (Tokalon Vineyard)', 'Well-structured', 'Blackcurrant, dark chocolate, hints of spice and oak', '14% - 15%', 2021, 700.00, 12, 'A distinguished red blend from the historic Tokalon Vineyard, established in 1966. The 2021 vintage is a powerful yet elegant wine, aged in big oak and French oak barrels, showcasing layers of dark fruit and spice', FALSE, TRUE, '/wines/the-reserve-red-blend.jpg'),
(9, 'Franschhoek Cellar Sauvignon Blanc', 'Sauvignon Blanc', 'South Africa (Franschhoek Valley)', 'Crisp', 'Crisp, light to medium-bodied with bright acidity', '12.5% - 13.5% ABV', 2021, 179.00, 18, 'A vibrant and refreshing Sauvignon Blanc from the renowned Franschhoek Valley. This wine showcases intense tropical fruit aromas with zesty citrus and a crisp, mineral-driven finish.', FALSE, TRUE, '/wines/franschhoek-cellar-sauvignon-blanc.jpg'),
(10, 'Billingham Big Oak', 'Red Blend / Cabernet Sauvignon-based blend', 'Mozambique', 'Full-bodied', 'Dark berries (blackberry, cassis), plum, dried figs, toasted oak, vanilla, and warm spice notes', '14% - 15.5% ABV', 2018, 527.00, 8, 'A bold and richly structured red wine from Mozambique, aged in big oak barrels to develop deep, complex flavours. This wine offers an intense bouquet of dark fruits, warm spices, and toasted vanilla.', FALSE, TRUE, '/wines/billingham-big-oak-red.jpg'),
(11, 'Grand Vin de Bordeaux - Médoc', 'Red Blend (Cabernet Sauvignon / Merlot-based)', 'France (Bordeaux - Médoc)', 'Full-bodied', 'Blackcurrant, cassis, cedar, tobacco, earthy notes, and subtle oak', '13% - 14% ABV', 2019, 416.00, 5, 'A prestigious Grand Vin from the renowned Médoc region of Bordeaux. This classified growth wine is crafted from select grapes and aged to perfection, offering a powerful yet elegant expression of the classic Bordeaux terroir.', FALSE, TRUE, '/wines/grand-vin-bordeaux-medoc.jpg'),
(12, 'Paco Milan', 'Red Blend / Tempranillo-based (Spanish style)', 'Spain (Hacienda Telespino)', 'Medium', 'Dark cherry, plum, blackberry, vanilla, spice, leather, and oak', '14% - 14.5% ABV', 2017, 319.00, 15, 'A distinguished red wine from Hacienda Telespino, crafted from carefully selected grapes. The 2017 vintage offers a rich and complex profile with dark fruit flavours, warm spices, and subtle oak influence.', FALSE, TRUE, '/wines/paco-milan.jpg')
ON CONFLICT (id) DO NOTHING;

-- ------------------------------------------------------------------------------
-- 3. SUBSCRIPTION PLANS
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS public.subscription_plans (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    tier_name TEXT NOT NULL,
    display_name TEXT NOT NULL,
    tagline TEXT,
    price NUMERIC(10,2) NOT NULL,
    wines_per_month INT DEFAULT 1,
    description TEXT,
    features TEXT[],
    packaging TEXT,
    savings_percent INT DEFAULT 0,
    is_popular BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    expiry_days INT DEFAULT 30,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

INSERT INTO public.subscription_plans (id, tier_name, display_name, tagline, price, wines_per_month, description, features, packaging, savings_percent, is_popular, display_order, expiry_days, is_active) OVERRIDING SYSTEM VALUE VALUES
(1, 'explorer', 'Essential Elegance Box', 'Discover. Enjoy. Belong.', 499.00, 2, 'Curated selections for the curious wine lover.', ARRAY['2 Premium Wines each month', 'Sommelier Tasting Notes', 'Complimentary Doorstep Delivery', 'Member Discounts on Full Cases'], 'Branded Kraft Delivery Box', 0, FALSE, 1, 30, TRUE),
(2, 'connoisseur', 'Vineyard Voyager Box', 'Exceptional Wines. Exclusive Access.', 999.00, 4, 'Hand-picked reserve bottles from top estates.', ARRAY['4 Reserve Wines each month', 'Detailed Pairing Guides', 'Priority Free Delivery', 'Exclusive Early Access to New Releases', '10% Off All Bottle Orders'], 'Luxury Magnetic Gift Box', 20, TRUE, 2, 30, TRUE),
(3, 'collector', 'Luxury Reserve Box', 'The Finest. The Rarest. Yours.', 1999.00, 6, 'Rare vintages and cellar-worthy masterpieces.', ARRAY['6 Rare & Award-Winning Vintages', 'Dedicated Personal Wine Advisor', 'VIP Invitation to Private Tasting Events', 'Bespoke Wooden Presentation Case', '15% Off Everything'], 'Premium Leatherette Presentation Box', 30, FALSE, 3, 30, TRUE),
(4, 'grand', 'Grand Reserve Society', 'More Than Wine. It''s a Lifestyle.', 4999.00, 12, 'The ultimate luxury wine club in Southern Africa.', ARRAY['12 Iconic Collector Vintages', 'Private Sommelier Wine Tasting at Home', 'First Allocation on Rare Imports', 'Custom Engraved Wine Storage Accessories', '20% Lifetime VIP Discount'], 'Handcrafted Wooden Wine Case', 40, FALSE, 4, 30, TRUE)
ON CONFLICT (id) DO NOTHING;

-- ------------------------------------------------------------------------------
-- 4. FOOD PAIRINGS
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS public.pairings (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name TEXT NOT NULL,
    description TEXT,
    price NUMERIC(10,2) NOT NULL,
    compatible_wines TEXT,
    in_stock BOOLEAN DEFAULT TRUE,
    image_url TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

INSERT INTO public.pairings (id, name, description, price, compatible_wines, in_stock, image_url) OVERRIDING SYSTEM VALUE VALUES
(1, 'Artisan Cheese Board', 'Aged cheddar, brie, and gorgonzola blue cheese with gourmet crackers and fig preserve.', 450.00, 'Kanonkop Pinotage, Meerlust Rubicon', TRUE, '/pairings/cheese-board.jpg'),
(2, 'Dark Chocolate Truffles', 'Handmade Belgian 70% dark chocolate truffles dusted with sea salt and cocoa.', 342.00, 'Grand Vin de Bordeaux - Médoc, Opus One', TRUE, '/pairings/truffles.jpg'),
(3, 'Biltong Platter', 'Traditional prime beef South African sliced biltong and droëwors selection.', 360.00, 'Cabernet Sauvignon, Pinotage, Shiraz', TRUE, '/wines/the-reserve-red-blend.jpg'),
(4, 'Mediterranean Olive Medley', 'Marinated Kalamata and green olives with rosemary, garlic, and cold-pressed olive oil.', 100.00, 'Franschhoek Cellar Sauvignon Blanc, Cloudy Bay', TRUE, '/wines/franschhoek-cellar-sauvignon-blanc.jpg')
ON CONFLICT (id) DO NOTHING;

-- ------------------------------------------------------------------------------
-- 5. CORPORATE GIFTS & GIFT BASKETS
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS public.corporate_gifts (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name TEXT NOT NULL,
    tier TEXT NOT NULL,
    description TEXT,
    features TEXT,
    price NUMERIC(10,2) NOT NULL,
    wines_included INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    image_url TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

INSERT INTO public.corporate_gifts (id, name, tier, description, features, price, wines_included, is_active, image_url) OVERRIDING SYSTEM VALUE VALUES
(1, 'Executive Gift Box', 'Executive', '3 Premium Wines paired with artisan chocolates and custom gift card.', '3 Premium Wines, Personalised Card, Elegant Ribbon Box', 1499.00, 3, TRUE, '/corporate/executive-gift-box.png'),
(2, 'Boardroom Collection', 'Boardroom', '6 Reserve Wines with gourmet accompaniments and crystal decanter.', '6 Reserve Wines, Gourmet Hamper, Crystal Decanter, Custom Company Branding', 3499.00, 6, TRUE, '/corporate/boardroom-collection.png'),
(3, 'Chairman''s Reserve', 'Chairman''s Reserve', '12 Rare Vintages in handcrafted wooden case with private tasting session.', '12 Rare Vintages, Handcrafted Wooden Case, Private Sommelier Tasting Event', 7999.00, 12, TRUE, '/corporate/chairmans-reserve.png')
ON CONFLICT (id) DO NOTHING;

CREATE TABLE IF NOT EXISTS public.gift_baskets (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    name TEXT NOT NULL,
    description TEXT,
    features TEXT,
    price NUMERIC(10,2) NOT NULL,
    wines_included INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    image_url TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

INSERT INTO public.gift_baskets (id, name, description, features, price, wines_included, is_active, image_url) OVERRIDING SYSTEM VALUE VALUES
(1, 'Classic Elegance Basket', 'The perfect gift for any celebration or milestone.', '2 wines, artisan chocolates, gourmet cheese selection', 699.00, 2, TRUE, '/baskets/classic-elegance-basket.png'),
(2, 'Premium Indulgence Basket', 'Luxury gift hamper for discerning wine and food lovers.', '3 reserve wines, charcuterie, artisan crackers & olive tapenade', 1299.00, 3, TRUE, '/baskets/premium-indulgence-basket.png'),
(3, 'Grand Celebration Hamper', 'The ultimate celebratory wine and delicacy experience.', '6 estate wines, gourmet hamper, crystal glasses & wine accessories', 2499.00, 6, TRUE, '/baskets/grand-celebration-hamper.png')
ON CONFLICT (id) DO NOTHING;

-- ------------------------------------------------------------------------------
-- 6. MAGAZINE SETTINGS & DOWNLOADS
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS public.magazine_settings (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    setting_key TEXT UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

INSERT INTO public.magazine_settings (setting_key, setting_value) VALUES
('pdf_path', '/downloads/WineCo_Boutique_Magazine_Professional_Edition.pdf'),
('cover_image', '/images/magazine-cover.jpg'),
('download_fee', '45.00')
ON CONFLICT (setting_key) DO UPDATE SET setting_value = EXCLUDED.setting_value;

CREATE TABLE IF NOT EXISTS public.magazine_downloads (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    customer_name TEXT,
    customer_email TEXT,
    customer_phone TEXT,
    payment_method TEXT DEFAULT 'stripe',
    payment_intent_id TEXT,
    amount NUMERIC(10,2) DEFAULT 45.00,
    status TEXT DEFAULT 'completed',
    downloaded_at TIMESTAMPTZ DEFAULT NOW()
);

-- ------------------------------------------------------------------------------
-- 7. CART, ORDERS & ORDER ITEMS
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS public.cart (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    session_id TEXT NOT NULL,
    user_id UUID REFERENCES auth.users(id) ON DELETE CASCADE,
    product_id BIGINT NOT NULL,
    product_type TEXT NOT NULL DEFAULT 'wine',
    product_name TEXT NOT NULL,
    price NUMERIC(10,2) NOT NULL,
    quantity INT DEFAULT 1,
    image_url TEXT,
    added_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS public.orders (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    order_number TEXT UNIQUE NOT NULL,
    user_id UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    customer_name TEXT NOT NULL,
    customer_email TEXT NOT NULL,
    customer_phone TEXT NOT NULL,
    customer_address TEXT NOT NULL,
    city TEXT DEFAULT 'Eswatini',
    items JSONB NOT NULL,
    subtotal NUMERIC(10,2) NOT NULL,
    tax NUMERIC(10,2) DEFAULT 0.00,
    shipping NUMERIC(10,2) DEFAULT 0.00,
    total NUMERIC(10,2) NOT NULL,
    status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled')),
    payment_method TEXT DEFAULT 'cash_on_delivery',
    payment_status TEXT DEFAULT 'pending' CHECK (payment_status IN ('pending', 'paid', 'failed', 'refunded')),
    stripe_session_id TEXT,
    stripe_payment_intent_id TEXT,
    notes TEXT,
    receipt_number TEXT,
    pop_file TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS public.order_items (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    order_id BIGINT REFERENCES public.orders(id) ON DELETE CASCADE,
    product_id BIGINT,
    product_type TEXT DEFAULT 'wine',
    product_name TEXT NOT NULL,
    price NUMERIC(10,2) NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- ------------------------------------------------------------------------------
-- 8. SUBSCRIPTION REQUESTS
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS public.subscription_requests (
    id BIGINT GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    user_id UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    plan_id BIGINT REFERENCES public.subscription_plans(id),
    plan_name TEXT NOT NULL,
    price NUMERIC(10,2) NOT NULL,
    full_name TEXT NOT NULL,
    email TEXT NOT NULL,
    phone TEXT NOT NULL,
    address TEXT NOT NULL,
    city TEXT NOT NULL,
    id_number TEXT,
    payment_method TEXT DEFAULT 'stripe',
    payment_status TEXT DEFAULT 'paid',
    stripe_payment_intent_id TEXT,
    pop_path TEXT,
    start_date TIMESTAMPTZ DEFAULT NOW(),
    expiry_date TIMESTAMPTZ DEFAULT (NOW() + INTERVAL '30 days'),
    status TEXT DEFAULT 'approved' CHECK (status IN ('pending', 'approved', 'active', 'expired', 'cancelled')),
    admin_notes TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- ------------------------------------------------------------------------------
-- 9. ROW LEVEL SECURITY (RLS) POLICIES
-- ------------------------------------------------------------------------------
ALTER TABLE public.wines ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.subscription_plans ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.pairings ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.corporate_gifts ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.gift_baskets ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.magazine_settings ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.orders ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.cart ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.profiles ENABLE ROW LEVEL SECURITY;

-- Public read policies
CREATE POLICY "Public can view wines" ON public.wines FOR SELECT USING (true);
CREATE POLICY "Public can view subscription_plans" ON public.subscription_plans FOR SELECT USING (is_active = true);
CREATE POLICY "Public can view pairings" ON public.pairings FOR SELECT USING (in_stock = true);
CREATE POLICY "Public can view corporate_gifts" ON public.corporate_gifts FOR SELECT USING (is_active = true);
CREATE POLICY "Public can view gift_baskets" ON public.gift_baskets FOR SELECT USING (is_active = true);
CREATE POLICY "Public can view magazine_settings" ON public.magazine_settings FOR SELECT USING (true);

-- Cart policies
CREATE POLICY "Cart select access" ON public.cart FOR SELECT USING (true);
CREATE POLICY "Cart insert access" ON public.cart FOR INSERT WITH CHECK (true);
CREATE POLICY "Cart update access" ON public.cart FOR UPDATE USING (true);
CREATE POLICY "Cart delete access" ON public.cart FOR DELETE USING (true);

-- Orders policies
CREATE POLICY "Orders insert access" ON public.orders FOR INSERT WITH CHECK (true);
CREATE POLICY "Orders select access" ON public.orders FOR SELECT USING (true);
CREATE POLICY "Orders update access" ON public.orders FOR UPDATE USING (true);
