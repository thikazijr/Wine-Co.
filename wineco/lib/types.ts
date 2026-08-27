export interface Wine {
  id: number;
  name: string;
  variety: string;
  origin: string;
  structure?: string;
  taste?: string;
  strength?: string;
  vintage?: number;
  price: number;
  stock_quantity: number;
  description?: string;
  featured: boolean;
  in_stock: boolean;
  image_url: string;
  created_at?: string;
}

export interface SubscriptionPlan {
  id: number;
  tier_name: string;
  display_name: string;
  tagline?: string;
  price: number;
  wines_per_month: number;
  description?: string;
  features?: string[];
  packaging?: string;
  savings_percent?: number;
  is_popular?: boolean;
  display_order?: number;
  expiry_days?: number;
  is_active?: boolean;
}

export interface Pairing {
  id: number;
  name: string;
  description: string;
  price: number;
  compatible_wines?: string;
  in_stock: boolean;
  image_url: string;
}

export interface CorporateGift {
  id: number;
  name: string;
  tier: string;
  description: string;
  features: string;
  price: number;
  wines_included: number;
  is_active: boolean;
  image_url: string;
}

export interface GiftBasket {
  id: number;
  name: string;
  description: string;
  features: string;
  price: number;
  wines_included: number;
  is_active: boolean;
  image_url: string;
}

export interface CartItem {
  id: number | string;
  product_id: number;
  product_type: 'wine' | 'pairing' | 'corporate' | 'basket' | 'magazine';
  product_name: string;
  price: number;
  quantity: number;
  image_url: string;
}

export interface Order {
  id?: number;
  order_number: string;
  customer_name: string;
  customer_email: string;
  customer_phone: string;
  customer_address: string;
  city?: string;
  items: CartItem[];
  subtotal: number;
  tax: number;
  shipping: number;
  total: number;
  status: 'pending' | 'processing' | 'shipped' | 'delivered' | 'completed' | 'cancelled';
  payment_method: 'cash_on_delivery' | 'bank_transfer' | 'stripe';
  payment_status: 'pending' | 'paid' | 'failed' | 'refunded';
  notes?: string;
  receipt_number?: string;
  created_at?: string;
}
