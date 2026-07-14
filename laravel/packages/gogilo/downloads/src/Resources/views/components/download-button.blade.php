<button type="button" 
        class="download-button {{ $class ?? '' }}"
        data-file-id="{{ $fileId }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => 'download-button']) }}>
    {{ $buttonText ?? 'Download' }}
    @if($showSpinner)
        <span class="spinner hidden" role="status">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </span>
    @endif
</button>

@push('download-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.download-button').forEach(button => {
            button.addEventListener('click', function(e) {
                const fileId = this.getAttribute('data-file-id');
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/downloads/' + fileId;
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (csrfToken) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = '_token';
                    input.value = csrfToken.content;
                    form.appendChild(input);
                }
                
                document.body.appendChild(form);
                form.submit();
            });
        });
    });
</script>
@endpush