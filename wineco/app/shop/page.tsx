'use client';

import React, { useState, useMemo } from 'react';
import Image from 'next/image';
import Link from 'next/link';
import { Search, SlidersHorizontal, ShoppingBag, Eye, X, CheckCircle, Wine as WineIcon } from 'lucide-react';
import { INITIAL_WINES } from '@/lib/mock-data';
import { Wine } from '@/lib/types';
import { useCart } from '@/lib/cart-context';

export default function ShopPage() {
  const { addToCart } = useCart();
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedVariety, setSelectedVariety] = useState('all');
  const [selectedOrigin, setSelectedOrigin] = useState('all');
  const [sortBy, setSortBy] = useState<'featured' | 'price-asc' | 'price-desc' | 'name'>('featured');
  const [selectedWine, setSelectedWine] = useState<Wine | null>(null);

  const varieties = [
    'all',
    'Sauvignon Blanc',
    'Pinotage',
    'Cabernet Sauvignon',
    'Red Blend',
    'Bordeaux Blend',
    'Cape Blend',
    "Montepulciano d'Abruzzo",
    'Tempranillo Blend',
  ];

  const origins = ['all', 'South Africa', 'France', 'New Zealand', 'Italy', 'USA', 'Spain', 'Mozambique'];

  const filteredWines = useMemo(() => {
    return INITIAL_WINES.filter((wine) => {
      const matchesSearch =
        wine.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        wine.variety.toLowerCase().includes(searchQuery.toLowerCase()) ||
        wine.origin.toLowerCase().includes(searchQuery.toLowerCase()) ||
        (wine.description && wine.description.toLowerCase().includes(searchQuery.toLowerCase()));

      const matchesVariety =
        selectedVariety === 'all' || wine.variety.toLowerCase().includes(selectedVariety.toLowerCase());

      const matchesOrigin =
        selectedOrigin === 'all' || wine.origin.toLowerCase().includes(selectedOrigin.toLowerCase());

      return matchesSearch && matchesVariety && matchesOrigin;
    }).sort((a, b) => {
      if (sortBy === 'price-asc') return a.price - b.price;
      if (sortBy === 'price-desc') return b.price - a.price;
      if (sortBy === 'name') return a.name.localeCompare(b.name);
      return (b.featured ? 1 : 0) - (a.featured ? 1 : 0);
    });
  }, [searchQuery, selectedVariety, selectedOrigin, sortBy]);

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
      {/* Header Banner */}
      <div className="bg-gradient-to-r from-[#1a0f0f] via-[#3d2020] to-[#1a0f0f] rounded-3xl p-8 md:p-12 text-white border-2 border-[#c9a03d]/40 shadow-xl relative overflow-hidden">
        <div className="relative z-10 max-w-2xl space-y-2">
          <span className="text-xs font-bold text-[#c9a03d] uppercase tracking-widest">
            The Wine & Co. Cellar
          </span>
          <h1 className="text-3xl md:text-5xl font-serif font-bold text-white tracking-tight">
            Curated Wine Collection
          </h1>
          <p className="text-sm md:text-base text-white/80">
            Explore authentic estate imports from the Cape Winelands, France, New Zealand, Italy, and beyond.
          </p>
        </div>
      </div>

      {/* Search & Filters */}
      <div className="bg-white p-6 rounded-3xl border border-[#f0ece8] shadow-sm space-y-5">
        <div className="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
          {/* Search Input */}
          <div className="md:col-span-8 relative">
            <Search className="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400" />
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              placeholder="Search by wine name, variety, or country..."
              className="w-full pl-12 pr-4 py-3 bg-[#faf6f0] border border-[#e8e0d8] rounded-full text-sm focus:outline-none focus:border-[#722f37] focus:ring-2 focus:ring-[#722f37]/10"
            />
          </div>

          {/* Sort By */}
          <div className="md:col-span-4">
            <select
              value={sortBy}
              onChange={(e) => setSortBy(e.target.value as any)}
              className="w-full py-3 px-4 bg-[#faf6f0] border border-[#e8e0d8] rounded-full text-sm font-medium text-[#2c1a1a] focus:outline-none focus:border-[#722f37]"
            >
              <option value="featured">Sort by: Featured & Curated</option>
              <option value="price-asc">Price: Low to High</option>
              <option value="price-desc">Price: High to Low</option>
              <option value="name">Name: A to Z</option>
            </select>
          </div>
        </div>

        {/* Variety Chips */}
        <div className="space-y-2 pt-2 border-t border-[#f0ece8]">
          <span className="text-xs font-bold text-[#722f37] uppercase tracking-wider block">
            Filter by Grape Variety:
          </span>
          <div className="flex flex-wrap gap-2">
            {varieties.map((v) => (
              <button
                key={v}
                onClick={() => setSelectedVariety(v)}
                className={`px-4 py-1.5 rounded-full text-xs font-semibold transition-all capitalize ${
                  selectedVariety === v
                    ? 'bg-[#722f37] text-white shadow-md'
                    : 'bg-[#faf6f0] text-neutral-600 hover:bg-[#e8e0d8] hover:text-[#2c1a1a]'
                }`}
              >
                {v === 'all' ? 'All Varieties' : v}
              </button>
            ))}
          </div>
        </div>

        {/* Origin Chips */}
        <div className="space-y-2 pt-2 border-t border-[#f0ece8]">
          <span className="text-xs font-bold text-[#c9a03d] uppercase tracking-wider block">
            Filter by Wine Region / Country:
          </span>
          <div className="flex flex-wrap gap-2">
            {origins.map((o) => (
              <button
                key={o}
                onClick={() => setSelectedOrigin(o)}
                className={`px-4 py-1.5 rounded-full text-xs font-semibold transition-all capitalize ${
                  selectedOrigin === o
                    ? 'bg-[#c9a03d] text-[#1a1a2e] shadow-md font-bold'
                    : 'bg-[#faf6f0] text-neutral-600 hover:bg-[#e8e0d8] hover:text-[#2c1a1a]'
                }`}
              >
                {o === 'all' ? 'All Regions' : o}
              </button>
            ))}
          </div>
        </div>
      </div>

      {/* Results Count */}
      <div className="flex items-center justify-between text-xs text-neutral-500 font-medium px-2">
        <span>Showing {filteredWines.length} bottles in cellar</span>
        {(selectedVariety !== 'all' || selectedOrigin !== 'all' || searchQuery) && (
          <button
            onClick={() => {
              setSelectedVariety('all');
              setSelectedOrigin('all');
              setSearchQuery('');
            }}
            className="text-[#722f37] font-semibold hover:underline"
          >
            Clear all filters
          </button>
        )}
      </div>

      {/* Wine Grid */}
      {filteredWines.length === 0 ? (
        <div className="bg-white rounded-3xl p-16 text-center space-y-4 border border-[#f0ece8]">
          <div className="w-16 h-16 rounded-full bg-[#faf6f0] text-[#722f37] mx-auto flex items-center justify-center">
            <WineIcon className="w-8 h-8" />
          </div>
          <h3 className="text-xl font-serif font-bold text-[#2c1a1a]">No wines found</h3>
          <p className="text-sm text-neutral-500">
            Try adjusting your search criteria or grape variety filter.
          </p>
          <button
            onClick={() => {
              setSelectedVariety('all');
              setSelectedOrigin('all');
              setSearchQuery('');
            }}
            className="btn-wine text-xs mt-2"
          >
            Reset Filters
          </button>
        </div>
      ) : (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          {filteredWines.map((wine) => (
            <div
              key={wine.id}
              className="bg-white rounded-3xl p-5 border border-[#f0ece8] shadow-sm hover:shadow-xl hover:border-[#c9a03d]/40 transition-all duration-300 flex flex-col justify-between group"
            >
              <div className="space-y-4">
                <div className="relative h-64 bg-[#faf6f0] rounded-2xl p-4 flex items-center justify-center overflow-hidden border border-[#f5efe8]">
                  {wine.vintage && (
                    <span className="absolute top-3 left-3 bg-[#722f37] text-white text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider shadow-sm">
                      {wine.vintage} Vintage
                    </span>
                  )}
                  {wine.featured && (
                    <span className="absolute top-3 right-3 bg-[#c9a03d] text-[#1a1a2e] text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">
                      Featured
                    </span>
                  )}
                  <Image
                    src={wine.image_url}
                    alt={wine.name}
                    width={180}
                    height={240}
                    className="object-contain max-h-56 group-hover:scale-108 transition-transform duration-500 drop-shadow-md"
                  />
                  {/* Quick View Button */}
                  <button
                    onClick={() => setSelectedWine(wine)}
                    className="absolute bottom-3 inset-x-4 bg-white/90 hover:bg-white text-[#2c1a1a] text-xs font-semibold py-2 rounded-xl shadow-md backdrop-blur-xs flex items-center justify-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity"
                  >
                    <Eye className="w-3.5 h-3.5" />
                    <span>Quick Tasting Notes</span>
                  </button>
                </div>

                <div className="space-y-1">
                  <span className="text-xs text-[#c9a03d] font-semibold tracking-wide block truncate">
                    {wine.variety}
                  </span>
                  <h3 className="text-base font-serif font-bold text-[#2c1a1a] group-hover:text-[#722f37] transition-colors truncate">
                    {wine.name}
                  </h3>
                  <p className="text-xs text-neutral-500 truncate">{wine.origin}</p>
                  {wine.taste && (
                    <p className="text-[11px] text-neutral-400 line-clamp-1 italic pt-0.5">
                      Notes: {wine.taste}
                    </p>
                  )}
                </div>
              </div>

              <div className="pt-4 mt-4 border-t border-[#f0ece8] flex items-center justify-between">
                <div>
                  <span className="text-xs text-neutral-400 block">Price</span>
                  <span className="text-lg font-bold text-[#1a6b3c]">
                    E{wine.price.toFixed(2)}
                  </span>
                </div>

                <button
                  onClick={() =>
                    addToCart({
                      product_id: wine.id,
                      product_type: 'wine',
                      product_name: wine.name,
                      price: wine.price,
                      image_url: wine.image_url,
                    })
                  }
                  className="btn-wine text-xs px-3.5 py-2 shadow-md"
                >
                  <ShoppingBag className="w-3.5 h-3.5" />
                  <span>Add to Bag</span>
                </button>
              </div>
            </div>
          ))}
        </div>
      )}

      {/* Wine Detail Modal */}
      {selectedWine && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-xs animate-in fade-in">
          <div className="bg-white rounded-3xl max-w-2xl w-full p-6 md:p-8 shadow-2xl border border-[#c9a03d] relative max-h-[90vh] overflow-y-auto">
            <button
              onClick={() => setSelectedWine(null)}
              className="absolute top-4 right-4 p-2 text-neutral-400 hover:text-neutral-700 rounded-full hover:bg-neutral-100 transition-colors"
            >
              <X className="w-5 h-5" />
            </button>

            <div className="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
              <div className="md:col-span-5 bg-[#faf6f0] rounded-2xl p-6 flex items-center justify-center border border-[#f0ece8]">
                <Image
                  src={selectedWine.image_url}
                  alt={selectedWine.name}
                  width={220}
                  height={300}
                  className="object-contain max-h-72 drop-shadow-xl"
                />
              </div>

              <div className="md:col-span-7 space-y-4">
                <div>
                  <span className="text-xs font-bold text-[#c9a03d] uppercase tracking-widest">
                    {selectedWine.variety} • {selectedWine.vintage}
                  </span>
                  <h2 className="text-2xl font-serif font-bold text-[#2c1a1a] mt-1">
                    {selectedWine.name}
                  </h2>
                  <p className="text-xs text-neutral-500">{selectedWine.origin}</p>
                </div>

                <div className="text-2xl font-bold text-[#1a6b3c]">
                  E{selectedWine.price.toFixed(2)}
                </div>

                <p className="text-xs text-neutral-600 leading-relaxed">
                  {selectedWine.description}
                </p>

                <div className="bg-[#faf6f0] p-4 rounded-xl space-y-1.5 text-xs border border-[#f0ece8]">
                  {selectedWine.structure && (
                    <div className="flex justify-between">
                      <span className="text-neutral-500">Body & Structure:</span>
                      <span className="font-semibold text-[#2c1a1a]">{selectedWine.structure}</span>
                    </div>
                  )}
                  {selectedWine.strength && (
                    <div className="flex justify-between">
                      <span className="text-neutral-500">Alcohol by Volume:</span>
                      <span className="font-semibold text-[#2c1a1a]">{selectedWine.strength}</span>
                    </div>
                  )}
                  {selectedWine.taste && (
                    <div className="pt-1 border-t border-[#e8e0d8] text-neutral-700">
                      <strong className="text-[#722f37] block mb-0.5">Tasting Profile:</strong>
                      <span>{selectedWine.taste}</span>
                    </div>
                  )}
                </div>

                <div className="flex gap-3 pt-2">
                  <button
                    onClick={() => {
                      addToCart({
                        product_id: selectedWine.id,
                        product_type: 'wine',
                        product_name: selectedWine.name,
                        price: selectedWine.price,
                        image_url: selectedWine.image_url,
                      });
                      setSelectedWine(null);
                    }}
                    className="btn-wine flex-1 py-3 text-sm"
                  >
                    <ShoppingBag className="w-4 h-4" />
                    <span>Add to Shopping Bag</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
