// stores/quote.ts - COMPLETELY UPDATED VERSION
import { iProduct, iQuoteItem } from '@/interfaces';
import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

export const useQuoteStore = defineStore('quote', () => {
    const items = ref<iQuoteItem[]>([]);
    const isInitialized = ref(false);
    const draftQuote = ref<{
        name: string;
        email: string;
        phone: string;
        company?: string;
        message: string;
    } | null>(null);

    // Generate unique ID
    const generateId = () => {
        return Date.now() + Math.floor(Math.random() * 1000);
    };

    // Initialize the store
    const initialize = () => {
        // Only run on client side
        if (typeof window !== 'undefined') {
            // Load quote items
            const savedItems = localStorage.getItem('quote_items');
            if (savedItems) {
                try {
                    const parsedItems = JSON.parse(savedItems);
                    // Ensure all items have an ID
                    items.value = parsedItems.map((item: iQuoteItem) => ({
                        ...item,
                        id: item.id || generateId(),
                        // Ensure product property exists
                        product: item.product || null,
                    }));
                } catch (error) {
                    console.error(
                        'Error parsing quote items from localStorage:',
                        error,
                    );
                    items.value = [];
                }
            }

            // Load draft quote form
            const savedDraft = localStorage.getItem('quote_draft');
            if (savedDraft) {
                try {
                    draftQuote.value = JSON.parse(savedDraft);
                } catch (error) {
                    console.error(
                        'Error parsing quote draft from localStorage:',
                        error,
                    );
                    draftQuote.value = null;
                }
            }

            isInitialized.value = true;
        }
    };

    // Save to localStorage whenever items change
    const saveToLocalStorage = () => {
        if (typeof window !== 'undefined') {
            localStorage.setItem('quote_items', JSON.stringify(items.value));
        }
    };

    // Save draft quote to localStorage
    const saveDraftToLocalStorage = () => {
        if (typeof window !== 'undefined' && draftQuote.value) {
            localStorage.setItem(
                'quote_draft',
                JSON.stringify(draftQuote.value),
            );
        }
    };

    // Total count of items (sum of quantities)
    const totalItems = computed(() => {
        return items.value.reduce((total, item) => total + item.quantity, 0);
    });

    // Unique products count
    const uniqueProductsCount = computed(() => {
        return items.value.length;
    });

    // Calculate total value
    const totalValue = computed(() => {
        return items.value.reduce((total, item) => {
            const price = item.price || getProductPrice(item.product);
            return total + price * item.quantity;
        }, 0);
    });

    // Helper to get product price
    const getProductPrice = (product?: iProduct | null): number => {
        if (!product) return 0;
        if (typeof product.price === 'string') {
            return parseFloat(product.price) || 0;
        }
        return product.price || 0;
    };

    // Add item to quote
    const addItem = (
        product: iProduct,
        quantity: number = 1,
        notes?: string,
    ) => {
        const existingItem = items.value.find(
            (i) => i.product_id === product.id,
        );

        if (existingItem) {
            // Update existing item
            existingItem.quantity += quantity;
            if (notes) {
                existingItem.notes = existingItem.notes
                    ? `${existingItem.notes}, ${notes}`
                    : notes;
            }
        } else {
            // Create new item
            const newItem: iQuoteItem = {
                id: generateId(),
                product_id: product.id,
                product: product,
                quantity: quantity,
                price: getProductPrice(product),
                notes: notes,
            };
            items.value.push(newItem);
        }

        saveToLocalStorage();
        dispatchQuoteUpdatedEvent();
    };

    // Update item quantity - FIXED VERSION
    const updateQuantity = (id: number, quantity: number) => {
        const item = items.value.find((i) => i.id === id);
        if (item) {
            item.quantity = Math.max(1, quantity); // Ensure at least 1
            saveToLocalStorage();
            dispatchQuoteUpdatedEvent();
        }
    };

    // Update item by product ID (alternative method)
    const updateQuantityByProductId = (productId: number, quantity: number) => {
        const item = items.value.find((i) => i.product_id === productId);
        if (item) {
            item.quantity = Math.max(1, quantity);
            saveToLocalStorage();
            dispatchQuoteUpdatedEvent();
        }
    };

    // Update item notes
    const updateNotes = (id: number, notes: string) => {
        const item = items.value.find((i) => i.id === id);
        if (item) {
            item.notes = notes;
            saveToLocalStorage();
        }
    };

    // Remove item from quote by item ID
    const removeItem = (id: number) => {
        const index = items.value.findIndex((i) => i.id === id);
        if (index !== -1) {
            items.value.splice(index, 1);
            saveToLocalStorage();
            dispatchQuoteUpdatedEvent();
        }
    };

    // Remove item by product ID
    const removeItemByProductId = (productId: number) => {
        const index = items.value.findIndex((i) => i.product_id === productId);
        if (index !== -1) {
            items.value.splice(index, 1);
            saveToLocalStorage();
            dispatchQuoteUpdatedEvent();
        }
    };

    // Clear all items
    const clearItems = () => {
        items.value = [];
        saveToLocalStorage();
        dispatchQuoteUpdatedEvent();
    };

    // Save draft quote form
    const saveDraft = (formData: {
        name: string;
        email: string;
        phone: string;
        company?: string;
        message: string;
    }) => {
        draftQuote.value = formData;
        saveDraftToLocalStorage();
    };

    // Clear draft quote form
    const clearDraft = () => {
        draftQuote.value = null;
        if (typeof window !== 'undefined') {
            localStorage.removeItem('quote_draft');
        }
    };

    // Clear everything (items and draft)
    const clearAll = () => {
        clearItems();
        clearDraft();
    };

    // Check if item is already in quote
    const isInQuote = (productId: number) => {
        return items.value.some((item) => item.product_id === productId);
    };

    // Get item by product ID
    const getItemByProductId = (productId: number): iQuoteItem => {
        return items.value.find(
            (item) => item.product_id === productId,
        ) as iQuoteItem;
    };

    // Get item by item ID
    const getItemById = (id: number) => {
        return items.value.find((item) => item.id === id);
    };

    // Dispatch event for other components to listen to
    const dispatchQuoteUpdatedEvent = () => {
        if (typeof window !== 'undefined') {
            window.dispatchEvent(
                new CustomEvent('quote-updated', {
                    detail: {
                        items: items.value,
                        totalItems: totalItems.value,
                    },
                }),
            );
        }
    };

    // Prepare products for submission
    const prepareProductsForSubmission = () => {
        return items.value.map((item) => {
            const price = item.price || getProductPrice(item.product);
            return {
                product_id: item.product_id,
                quantity: item.quantity,
                price: price,
            };
        });
    };

    // Format items for display
    const formatItemsForDisplay = () => {
        return items.value.map((item) => ({
            ...item,
            total_price:
                (item.price || getProductPrice(item.product)) * item.quantity,
        }));
    };

    // Initialize when store is created
    if (typeof window !== 'undefined') {
        initialize();
    }

    return {
        items,
        draftQuote,
        totalItems,
        uniqueProductsCount,
        totalValue,
        isInQuote,
        getItemByProductId,
        getItemById,
        addItem,
        updateQuantity,
        updateQuantityByProductId,
        updateNotes,
        removeItem,
        removeItemByProductId,
        clearItems,
        saveDraft,
        clearDraft,
        clearAll,
        prepareProductsForSubmission,
        formatItemsForDisplay,
        initialize,
        isInitialized,
    };
});
