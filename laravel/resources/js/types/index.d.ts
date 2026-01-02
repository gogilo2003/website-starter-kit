import { iNotification } from '@/interfaces';
import { Config } from 'ziggy-js';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    ziggy: Config & { location: string };
    menu: {
        products: any[];
        resources: any[];
    };
    contact: {
        phone: string;
        email: string;
        address: string;
        location: string;
    };
    notification: iNotification;
};
