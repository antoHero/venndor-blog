interface PostImageProps {
    src: string | null;
    alt: string;
    className?: string;
    /**
     * Defaults to lazy — every call site is a below-the-fold grid card
     * except the featured post, which passes "eager" explicitly since it's
     * the page's LCP element and lazy-loading it would slow down the
     * first paint it's actually responsible for.
     */
    loading?: 'lazy' | 'eager';
}

export default function PostImage({
    src,
    alt,
    className,
    loading = 'lazy',
}: PostImageProps) {
    if (!src) {
        return (
            <div
                className={`flex items-center justify-center bg-brand-tint ${className ?? ''}`}
            >
                <img
                    src="/logo.svg"
                    alt=""
                    aria-hidden
                    className="h-8 w-auto opacity-40"
                />
            </div>
        );
    }

    return (
        <img
            src={src}
            alt={alt}
            loading={loading}
            decoding="async"
            fetchPriority={loading === 'eager' ? 'high' : 'auto'}
            className={`object-cover ${className ?? ''}`}
        />
    );
}
