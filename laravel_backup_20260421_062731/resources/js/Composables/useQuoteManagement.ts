import Swal from 'sweetalert2';

export function useQuoteManagement() {
    const confirmAction = async (options: {
        title?: string;
        text: string;
        confirmButtonText?: string;
        icon?: 'warning' | 'question';
    }) => {
        const result = await Swal.fire({
            title: options.title ?? 'Are you sure?',
            text: options.text,
            icon: options.icon ?? 'warning',
            showCancelButton: true,
            confirmButtonText: options.confirmButtonText ?? 'Yes, continue',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            focusCancel: true,
        });

        return result.isConfirmed;
    };

    const toast = (icon: 'success' | 'error', text: string) => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false,
            icon,
            text,
        });
    };

    const formatDate = (date: string | Date | undefined | null) => {
        if (!date) return 'N/A';
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };

    const formatDateTime = (date: string | Date | undefined | null) => {
        if (!date) return 'N/A';
        return new Date(date).toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    const formatCurrency = (amount: number | undefined) => {
        if (amount === undefined) amount = 0;
        return new Intl.NumberFormat('en-KE', {
            style: 'currency',
            currency: 'KES',
        }).format(amount);
    };

    const statusConfig = {
        pending: {
            label: 'Pending',
            color: 'bg-yellow-100 text-yellow-800 border-yellow-300',
        },
        sent: {
            label: 'Sent',
            color: 'bg-blue-100 text-blue-800 border-blue-300',
        },
        viewed: {
            label: 'Viewed',
            color: 'bg-purple-100 text-purple-800 border-purple-300',
        },
        completed: {
            label: 'Completed',
            color: 'bg-green-100 text-green-800 border-green-300',
        },
        rejected: {
            label: 'Rejected',
            color: 'bg-red-100 text-red-800 border-red-300',
        },
    };

    const statusOptions = [
        { value: 'pending', label: 'Pending' },
        { value: 'sent', label: 'Sent' },
        { value: 'viewed', label: 'Viewed' },
        { value: 'completed', label: 'Completed' },
        { value: 'rejected', label: 'Rejected' },
    ];

    const getStatusBadgeClass = (status: string) => {
        return (
            statusConfig[status as keyof typeof statusConfig]?.color ||
            'bg-gray-100 text-gray-800 border-gray-300'
        );
    };

    const getStatusLabel = (status: string) => {
        return (
            statusConfig[status as keyof typeof statusConfig]?.label || status
        );
    };

    const copyTrackingUrl = (url: string) => {
        navigator.clipboard.writeText(url).then(() => {
            toast('success', 'Tracking URL copied to clipboard');
        });
    };

    return {
        confirmAction,
        toast,
        formatDate,
        formatDateTime,
        formatCurrency,
        statusConfig,
        statusOptions,
        getStatusBadgeClass,
        getStatusLabel,
        copyTrackingUrl,
    };
}
