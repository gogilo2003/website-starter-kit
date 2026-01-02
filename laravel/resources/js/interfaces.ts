export interface iNotification {
    success?: string | null;
    info?: string | null;
    warning?: string | null;
    error?: string | null;
    danger?: string | null;
}

export interface iMenuItem {
    name: string;
    caption: string;
    icon: string;
    alt_names?: string[];
    active: boolean | false;
}

interface iPagination<T> {
    current_page: number;
    data: T[];
    first_page_url: string | null;
    from: number | null;
    last_page: number;
    last_page_url: string | null;
    links: iLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: 0;
}
export interface iContact {
    icon: string | null;
    picture: string | null;
    title: string;
    content: string;
}

export interface iElement {
    id: number;
    name: string;
    title: string;
    content?: string | '' | null;
    type?: string | '' | null;
    photo?: string | '' | null;
    icon?: string | '' | null;
    published?: boolean;
}
export type iElements = iPagination<iElement>;

export interface iPageSection {
    id: number;
    name: string;
    title: string;
    description: string;
    elements: iElement[];
}

interface iLink {
    url: string | null;
    label: string;
    active: boolean;
}
export type iPageSections = iPagination<iPageSection>;

export interface iDownloadCategory {
    id: number;
    name: string;
    slug: string;
    description: string;
    icon?: string;
    is_active: boolean;
}
export type iDownloadCategories = iPagination<iDownloadCategory>;
export interface iDownload {
    id: number;
    title: string;
    slug: string;
    description: string;
    path: string;
    name: string;
    type: string;
    size: number;
    download_count: number;
    is_featured: boolean;
    is_active: boolean;
    url?: string;
    category?: string;
}
export type iDownloads = iPagination<iDownload>;

export interface iNewsArticle {
    id: number;
    picture: string | null | '';
    date: string;
    author: string;
    title: string;
    content: string;
    url: string;
    published: boolean;
    front: boolean;
}

export type iNewsArticles = iPagination<iNewsArticle>;

export interface iAttribute {
    name: string;
    value: string;
}

export interface iPicture {
    id: number;
    name: string;
    caption: string;
    url?: string;
}
export interface iProduct {
    id: number;
    title: string;
    slug: string;
    summary: string;
    price?: number | string;
    description: string;
    picture: string | null;
    pictures?: iPicture[];
    url: string;
    published: boolean;
    front: boolean;
    features: iAttribute[];
    created_at: string | Date;
    updated_at: string | Date;
}

export type iProducts = iPagination<iProduct>;

export interface iSlide {
    id: number;
    title: string;
    caption: string;
    picture: string;
    media_type: string;
    published: boolean;
}

export type iSlides = iPagination<iSlide>;

export interface iProductCategory {
    id: number;
    slug: string;
    name: string;
    description: string;
    icon: string;
    picture: string;
    published: boolean;
    promoted: boolean;
    picture_url?: string;
}

export type iProductCategories = iPagination<iProductCategory>;

export interface iPartner {
    id: number;
    title: string;
    slug: string;
    logo: string;
    website: string;
    description: string;
    published: boolean;
    front: boolean;
}

export type iPartners = iPagination<iPartner>;

export interface iFunFact {
    label: string;
    value: number;
}

export interface iBrand {
    id: number;
    name: string;
    logo: string;
    logo_url?: string;
    created_at: string | Date;
    updated_at: string | Date;
}

export type iBrands = iPagination<iBrand>;

export interface iQuoteItem {
    id: number;
    product_id: number;
    product?: iProduct;
    quantity: number;
    price: number;
    notes?: string;
}

export interface iQuote {
    id: number;
    code: string;
    name: string;
    email: string;
    phone: string;
    company?: string;
    message: string;
    status: string; // Add status field
    status_label?: string; // Add for display
    status_color?: string; // Add for UI
    view_count?: number; // Add tracking views
    last_viewed_at?: string | Date | null; // Add last viewed
    total_amount?: number; // Add total calculation
    tracking_url?: string; // Add for sharing
    items?: iQuoteItem[]; // Changed from singular to array
    products?: iProduct;
    created_at?: string | Date;
    updated_at?: string | Date;
}

export type iQuotes = iPagination<iQuote>;

// Optional: Add wishlist interface
export interface iWishlistItem {
    id: number;
    product_id: number;
    product: iProduct;
    quantity: number;
}

// Optional: Add enum for status
export enum QuoteStatus {
    PENDING = 'pending',
    SENT = 'sent',
    VIEWED = 'viewed',
    ACCEPTED = 'accepted',
    REJECTED = 'rejected',
}

// Optional: Add type for quote form submission
export interface QuoteFormData {
    name: string;
    email: string;
    phone: string;
    company?: string;
    message: string;
    products: Array<{
        product_id: number;
        quantity: number;
        price: number;
    }>;
}
