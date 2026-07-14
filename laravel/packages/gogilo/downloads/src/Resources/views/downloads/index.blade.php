<div class="downloads-container">
    <h2>{{ $title ?? 'Downloads' }}</h2>
    
    @foreach($files as $file)
    <div class="download-card">
        <h3>{{ $file->original_filename }}</h3>
        <p>Size: {{ $file->formatted_size }}</p>
        <p>Downloads: {{ $file->formatted_download_count }}</p>
        <a href="{{ route('downloads.download', $file->id) }}" class="download-btn">
            Download
        </a>
    </div>
    @endforeach
</div>