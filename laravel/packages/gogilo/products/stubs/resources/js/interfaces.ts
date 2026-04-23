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

interface iLink {
    url: string | null;
    label: string;
    active: boolean;
}

