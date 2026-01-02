// utils/phoneFormatter.ts
/**
 * Parse a phone string that may contain multiple numbers separated by '|'
 * and return an array of formatted phone objects
 *
 * @param phoneString - Phone string like "+254722298105 | +254722720859" or "+254722298105"
 * @returns Array of objects containing tel link and human-readable display for each phone number
 */
export function parsePhoneNumbers(phoneString: string): Array<{
    telLink: string;
    humanReadable: string;
}> {
    // Handle null/undefined/empty input
    if (!phoneString?.trim()) {
        return [];
    }

    // Split by '|' and clean up each phone number
    const phoneNumbers = phoneString
        .split('|')
        .map((phone) => phone.trim())
        .filter((phone) => phone.length > 0);

    // Format each phone number
    return phoneNumbers.map((phone) => {
        // Ensure phone starts with + for tel link
        const telLink = phone.startsWith('+') ? phone : `+${phone}`;

        // Create human-readable format
        const humanReadable = formatPhoneForDisplay(phone);

        return { telLink, humanReadable };
    });
}

/**
 * Format a phone number for human-readable display
 * Example: +254722298105 -> +254 722 298105
 */
function formatPhoneForDisplay(phone: string): string {
    // Remove all non-digit characters except leading +
    const cleaned = phone.replace(/[^\d+]/g, '');

    // If it's a Kenya number (+254XXXXXXXXX)
    if (cleaned.startsWith('+254') && cleaned.length === 13) {
        const withoutCode = cleaned.substring(4); // Remove +254
        return `+254 ${withoutCode.substring(0, 3)} ${withoutCode.substring(3)}`;
    }

    // If it's a 10-digit number starting with 0 (Kenya)
    if (cleaned.startsWith('0') && cleaned.length === 10) {
        const withoutZero = cleaned.substring(1); // Remove leading 0
        return `+254 ${withoutZero.substring(0, 3)} ${withoutZero.substring(3)}`;
    }

    // If it's a 12-digit number (254XXXXXXXXX)
    if (cleaned.startsWith('254') && cleaned.length === 12) {
        const withoutCode = cleaned.substring(3); // Remove 254
        return `+254 ${withoutCode.substring(0, 3)} ${withoutCode.substring(3)}`;
    }

    // Default formatting for other international numbers
    const countryCodeMatch = cleaned.match(/^(\+\d{1,3})(\d+)$/);
    if (countryCodeMatch) {
        const [, countryCode, localNumber] = countryCodeMatch;

        // Format local number in groups of 3 digits
        let formattedLocal = '';
        for (let i = 0; i < localNumber.length; i += 3) {
            formattedLocal += ' ' + localNumber.substring(i, i + 3);
        }

        return `${countryCode}${formattedLocal.trim()}`;
    }

    // Fallback: return as is
    return phone;
}

/**
 * Get the primary phone number (first in the list)
 * Useful for when you need just one phone number for calling
 */
export function getPrimaryPhoneNumber(phoneString: string): {
    telLink: string;
    humanReadable: string;
} | null {
    const phones = parsePhoneNumbers(phoneString);
    return phones.length > 0 ? phones[0] : null;
}
