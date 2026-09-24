import type { BlogPost } from '@/types/blog';

interface FeaturedPostProps {
    post: BlogPost;
}

export default function FeaturedPost({ post }: FeaturedPostProps) {
    return (
        <div className="relative flex flex-col items-center gap-8 overflow-hidden lg:flex-row lg:gap-12">
            <img
                src="/assets/img/blog/featured-backdrop.jpg"
                alt=""
                aria-hidden
                className="pointer-events-none absolute inset-0 hidden size-full object-cover opacity-40 lg:block"
            />

            <div className="relative h-[280px] w-full shrink-0 overflow-hidden rounded-3xl sm:h-[360px] lg:h-[428px] lg:w-[736px]">
                <img
                    src={post.image}
                    alt={post.title}
                    className="size-full object-cover"
                />
                <div className="absolute inset-0 bg-linear-to-b from-black/0 from-62% to-black/40" />
            </div>

            <div className="relative flex flex-1 flex-col justify-center gap-6 py-2">
                <div className="flex items-center gap-1">
                    <p className="flex-1 font-brand text-sm text-brand-subtle">
                        {post.date} • {post.readTime} • {post.category}
                    </p>
                    {post.tag && (
                        <span className="flex shrink-0 items-center gap-1 font-brand text-sm font-medium text-brand-success">
                            <span className="size-2 rounded-full bg-brand-success" />
                            {post.tag}
                        </span>
                    )}
                </div>

                <div className="flex flex-col gap-2">
                    <h2 className="font-brand text-2xl leading-8 font-medium text-brand sm:text-[30px] sm:leading-[38px]">
                        {post.title}
                    </h2>
                    <p className="font-brand text-sm text-brand-ink">
                        {post.excerpt}
                    </p>
                </div>

                <a
                    href="#"
                    className="font-brand text-base font-medium text-brand-ink"
                >
                    <span className="underline decoration-solid underline-offset-4">
                        Continue Reading
                    </span>{' '}
                    →
                </a>
            </div>
        </div>
    );
}
