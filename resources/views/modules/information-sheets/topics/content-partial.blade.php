<link rel="stylesheet" href="{{ dynamic_asset('css/pages/content-partial.css') }}">
<div class="topic-content">
    <div class="topic-header mb-4">
        <h2 class="topic-title">{{ $topic->title }}</h2>
        <div class="topic-meta d-flex align-items-center gap-3 text-muted">
            <span class="topic-number">Topic {{ $topic->topic_number }}</span>
            <span class="topic-order">Order: {{ $topic->order }}</span>
        </div>
    </div>

    @if($topic->content)
    <div class="content-body basic-formatting mb-4">
        {!! $topic->content !!}
    </div>
    @endif

    @if($topic->parts && count($topic->parts) > 0)
    <div class="topic-parts">
        @foreach($topic->parts as $index => $part)
        <div class="part-item mb-4">
            <div class="row align-items-start">
                @if(!empty($part['image']))
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="part-image-wrapper">
                        <img src="{{ $part['image'] }}" alt="{{ $part['title'] ?? 'Part Image' }}" class="img-fluid rounded shadow-sm">
                    </div>
                </div>
                <div class="col-md-9">
                @else
                <div class="col-12">
                @endif
                    <div class="part-content">
                        @if(!empty($part['title']))
                        <h5 class="part-title mb-2">
                            <span class="part-number-badge me-2">{{ $index + 1 }}</span>
                            {{ $part['title'] }}
                        </h5>
                        @endif
                        @if(!empty($part['explanation']))
                        <div class="part-explanation">
                            {!! nl2br(e($part['explanation'])) !!}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @if(!$loop->last)
        <hr class="my-4">
        @endif
        @endforeach
    </div>
    @endif
</div>

<style>
.topic-parts {
    margin-top: 1.5rem;
}

.part-item {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 0.5rem;
    border-left: 4px solid #0d6efd;
}

.part-image-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 0.5rem;
}

.part-image-wrapper img {
    width: 100%;
    height: auto;
    max-height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.part-image-wrapper:hover img {
    transform: scale(1.05);
}

.part-number-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    background: #0d6efd;
    color: white;
    border-radius: 50%;
    font-size: 0.75rem;
    font-weight: 600;
}

.part-title {
    color: #333;
    font-weight: 600;
}

.part-explanation {
    color: #555;
    line-height: 1.7;
}
</style>
