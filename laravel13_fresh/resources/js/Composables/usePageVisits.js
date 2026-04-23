// resources/js/Composables/usePageVisits.js
import { onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

export default function usePageVisits() {
    const { props } = usePage()
    
    onMounted(() => {
        // You can access visit data if you want to display it
        console.log('Page visit tracked')
    })
    
    return {
        // You can expose visit statistics if needed
    }
}