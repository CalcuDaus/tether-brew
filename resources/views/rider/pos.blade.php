@extends('layouts.app')
@section('title', 'Point of Sale')

@section('content')
<div x-data="posApp()" x-cloak>
    <div class="grid-2">
        {{-- Product List --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" x2="6" y1="2" y2="4"/><line x1="10" x2="10" y1="2" y2="4"/><line x1="14" x2="14" y1="2" y2="4"/>
                    </svg> Menu - {{ $cart->name }}
                </h3>
                <div class="form-group m-0-w-200">
                    <input type="text" class="form-input p-2-3-text-md" placeholder="🔍 Cari menu..." x-model="search">
                </div>
            </div>
            <div class="card-body p-3-custom">
                <template x-if="filteredProducts.length === 0">
                    <div class="empty-state p-5-custom">
                        <div class="empty-state-icon">
                            <svg class="icon-two-tone" width="2em" height="2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" x2="6" y1="2" y2="4"/><line x1="10" x2="10" y1="2" y2="4"/><line x1="14" x2="14" y1="2" y2="4"/>
                            </svg>
                        </div>
                        <div class="empty-state-text">Tidak ada produk ditemukan</div>
                    </div>
                </template>

                <div class="grid-pos-items">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div @click="addToCart(product)"
                            
                             :style="product.stock <= 0 ? 'opacity: 0.4; pointer-events: none;' : ''"
                             @mouseenter="$el.style.borderColor='rgba(34,197,94,0.5)'; $el.style.transform='translateY(-2px)'"
                             @mouseleave="$el.style.borderColor='var(--border-color)'; $el.style.transform='none'" class="pos-item-card">
                            <div class="text-icon-lg">
                                <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" x2="6" y1="2" y2="4"/><line x1="10" x2="10" y1="2" y2="4"/><line x1="14" x2="14" y1="2" y2="4"/>
                                </svg>
                            </div>
                            <div x-text="product.name" class="text-primary-semi mb-1-custom"></div>
                            <div x-text="formatRupiah(product.price)" class="text-gold-bold"></div>
                            <div class="text-xs-muted mt-1-custom">
                                Stok: <span x-text="product.stock"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Cart / Order --}}
        <div class="card sticky-cart">
            <div class="card-header">
                <h3 class="card-title">
                    <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/>
                    </svg> Pesanan
                </h3>
                <span x-text="cartItems.length + ' item'" class="text-md-muted"></span>
            </div>
            <div class="card-body">
                <template x-if="cartItems.length === 0">
                    <div class="empty-state p-30-custom">
                        <div class="empty-state-icon"><svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg></div>
                        <div class="empty-state-text">Klik menu untuk menambahkan</div>
                    </div>
                </template>

                <template x-for="(item, index) in cartItems" :key="item.product_id">
                    <div class="flex-between py-3-custom border-bottom">
                        <div class="flex-1">
                            <div x-text="item.name" class="text-primary-semi"></div>
                            <div x-text="formatRupiah(item.price)" class="text-sm-muted text-gold-semi"></div>
                        </div>
                        <div class="flex-start-gap">
                            <button @click="decreaseQty(index)" class="btn-qty">−</button>
                            <span x-text="item.qty" class="qty-val"></span>
                            <button @click="increaseQty(index)" class="btn-qty">+</button>
                            <button @click="removeItem(index)" class="btn-qty-delete">✕</button>
                        </div>
                        <div x-text="formatRupiah(item.price * item.qty)" class="qty-total"></div>
                    </div>
                </template>

                <template x-if="cartItems.length > 0">
                    <div>
                        {{-- Total --}}
                        <div class="flex-between py-4-custom border-top-thick mt-2-custom">
                            <span class="text-primary-bold">Total</span>
                            <span x-text="formatRupiah(totalPrice)" class="total-value"></span>
                        </div>

                        {{-- Payment method --}}
                        <div class="form-group mt-3-custom">
                            <label class="form-label">Metode Pembayaran</label>
                            <div class="flex-gap-2">
                                <button @click="paymentMethod = 'cash'" class="btn flex-item-1 flex-center"
                                        :class="paymentMethod === 'cash' ? 'btn-primary' : 'btn-secondary'"
                                       >
                                    <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/>
                                    </svg> Cash
                                </button>
                                <button @click="paymentMethod = 'qris'" class="btn flex-item-1 flex-center"
                                        :class="paymentMethod === 'qris' ? 'btn-primary' : 'btn-secondary'"
                                       >
                                    <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/>
                                    </svg> QRIS
                                </button>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="form-group">
                            <label class="form-label">Catatan</label>
                            <input type="text" class="form-input" x-model="notes" placeholder="Opsional...">
                        </div>

                        {{-- Submit --}}
                        <form method="POST" action="{{ route('rider.pos.store') }}" x-ref="posForm">
                            @csrf
                            <template x-for="(item, index) in cartItems" :key="'form-'+item.product_id">
                                <div>
                                    <input type="hidden" :name="'items['+index+'][product_id]'" :value="item.product_id">
                                    <input type="hidden" :name="'items['+index+'][qty]'" :value="item.qty">
                                </div>
                            </template>
                            <input type="hidden" name="payment_method" :value="paymentMethod">
                            <input type="hidden" name="notes" :value="notes">

                            <button type="submit" class="btn btn-primary btn-block-lg"
                                    :disabled="cartItems.length === 0">
                                <svg class="icon-two-tone" width="1.2em" height="1.2em" viewBox="0 0 24 24" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/>
                                </svg> Bayar <span x-text="formatRupiah(totalPrice)"></span>
                            </button>
                        </form>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function posApp() {
    return {
        products: @json($products),
        cartItems: [],
        search: '',
        paymentMethod: 'cash',
        notes: '',

        get filteredProducts() {
            if (!this.search) return this.products;
            const q = this.search.toLowerCase();
            return this.products.filter(p => p.name.toLowerCase().includes(q));
        },

        get totalPrice() {
            return this.cartItems.reduce((sum, item) => sum + (item.price * item.qty), 0);
        },

        addToCart(product) {
            const existing = this.cartItems.find(i => i.product_id === product.id);
            if (existing) {
                if (existing.qty < product.stock) {
                    existing.qty++;
                }
            } else {
                this.cartItems.push({
                    product_id: product.id,
                    name: product.name,
                    price: product.price,
                    qty: 1,
                    maxStock: product.stock
                });
            }
        },

        increaseQty(index) {
            const item = this.cartItems[index];
            if (item.qty < item.maxStock) {
                item.qty++;
            }
        },

        decreaseQty(index) {
            if (this.cartItems[index].qty > 1) {
                this.cartItems[index].qty--;
            } else {
                this.removeItem(index);
            }
        },

        removeItem(index) {
            this.cartItems.splice(index, 1);
        },

        formatRupiah(num) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
        }
    };
}
</script>
@endpush
@endsection

