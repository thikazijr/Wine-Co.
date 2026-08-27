'use client';

import React, { createContext, useContext, useEffect, useState } from 'react';
import { CartItem } from './types';

interface CartContextType {
  cart: CartItem[];
  addToCart: (item: Omit<CartItem, 'quantity' | 'id'>, quantity?: number) => void;
  updateQuantity: (productId: number, productType: string, quantity: number) => void;
  removeFromCart: (productId: number, productType: string) => void;
  clearCart: () => void;
  totalCount: number;
  subtotal: number;
  deliveryFee: number;
  grandTotal: number;
  isDrawerOpen: boolean;
  setIsDrawerOpen: (open: boolean) => void;
  toastMessage: string | null;
  toastType: 'success' | 'error';
  showToast: (msg: string, type?: 'success' | 'error') => void;
}

const CartContext = createContext<CartContextType | undefined>(undefined);

export function CartProvider({ children }: { children: React.ReactNode }) {
  const [cart, setCart] = useState<CartItem[]>([]);
  const [isDrawerOpen, setIsDrawerOpen] = useState(false);
  const [toastMessage, setToastMessage] = useState<string | null>(null);
  const [toastType, setToastType] = useState<'success' | 'error'>('success');
  const [isLoaded, setIsLoaded] = useState(false);

  useEffect(() => {
    try {
      const stored = localStorage.getItem('wineco_cart');
      if (stored) {
        setCart(JSON.parse(stored));
      }
    } catch (e) {
      console.error('Failed to load cart from localStorage', e);
    }
    setIsLoaded(true);
  }, []);

  useEffect(() => {
    if (isLoaded) {
      localStorage.setItem('wineco_cart', JSON.stringify(cart));
    }
  }, [cart, isLoaded]);

  const showToast = (msg: string, type: 'success' | 'error' = 'success') => {
    setToastMessage(msg);
    setToastType(type);
    setTimeout(() => {
      setToastMessage(null);
    }, 3200);
  };

  const addToCart = (item: Omit<CartItem, 'quantity' | 'id'>, quantity = 1) => {
    setCart((prev) => {
      const existingIndex = prev.findIndex(
        (i) => i.product_id === item.product_id && i.product_type === item.product_type
      );

      if (existingIndex > -1) {
        const updated = [...prev];
        updated[existingIndex].quantity += quantity;
        return updated;
      }

      return [
        ...prev,
        {
          ...item,
          id: `${item.product_type}_${item.product_id}_${Date.now()}`,
          quantity,
        },
      ];
    });

    showToast(`Added "${item.product_name}" to your cart!`);
    setIsDrawerOpen(true);
  };

  const updateQuantity = (productId: number, productType: string, quantity: number) => {
    if (quantity <= 0) {
      removeFromCart(productId, productType);
      return;
    }

    setCart((prev) =>
      prev.map((item) =>
        item.product_id === productId && item.product_type === productType
          ? { ...item, quantity }
          : item
      )
    );
  };

  const removeFromCart = (productId: number, productType: string) => {
    setCart((prev) =>
      prev.filter(
        (item) => !(item.product_id === productId && item.product_type === productType)
      )
    );
    showToast('Item removed from cart');
  };

  const clearCart = () => {
    setCart([]);
  };

  const totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);
  const subtotal = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
  const deliveryFee = subtotal > 0 ? (subtotal >= 600 ? 0 : 50) : 0;
  const grandTotal = subtotal + deliveryFee;

  return (
    <CartContext.Provider
      value={{
        cart,
        addToCart,
        updateQuantity,
        removeFromCart,
        clearCart,
        totalCount,
        subtotal,
        deliveryFee,
        grandTotal,
        isDrawerOpen,
        setIsDrawerOpen,
        toastMessage,
        toastType,
        showToast,
      }}
    >
      {children}
    </CartContext.Provider>
  );
}

export function useCart() {
  const context = useContext(CartContext);
  if (!context) {
    throw new Error('useCart must be used within a CartProvider');
  }
  return context;
}
