import { Link } from '@inertiajs/react';
import PostImage from '@/components/blog/post-image';
import posts from '@/routes/posts';
import type { BlogPost } from '@/types/blog';

interface FeaturedPostProps {
    post: BlogPost;
}

export default function FeaturedPost({ post }: FeaturedPostProps) {
    const href = posts.show(post.slug);

    return (
        <div className="relative flex flex-col items-center gap-8 overflow-hidden lg:flex-row lg:gap-12">
            <img
                src="/assets/img/blog/featured-backdrop.jpg"
                alt=""
                aria-hidden
                className="pointer-events-none absolute inset-0 hidden size-full object-cover opacity-40 lg:block"
            />

            <Link
                href={href}
                className="relative block h-[280px] w-full shrink-0 overflow-hidden rounded-3xl sm:h-[360px] lg:h-[428px] lg:w-[736px]"
            >
                <PostImage
                    src={post.image}
                    alt={post.title}
                    className="size-full"
                    loading="eager"
                />
                <div className="absolute inset-0 bg-linear-to-b from-black/0 from-62% to-black/40" />
            </Link>

            <div className="relative flex flex-1 flex-col justify-center gap-6 py-2">
                <div className="flex items-center gap-1">
                    <p className="flex-1 font-brand text-sm text-brand-subtle">
                        {[post.date, post.readTime, post.category]
                            .filter(Boolean)
                            .join(' • ')}
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
                        <Link href={href} className="hover:underline">
                            {post.title}
                        </Link>
                    </h2>
                    {post.excerpt && (
                        <p className="font-brand text-sm text-brand-ink">
                            {post.excerpt}
                        </p>
                    )}
                </div>

                <Link
                    href={href}
                    className="font-brand text-base font-medium text-brand-ink"
                >
                    <span className="underline decoration-solid underline-offset-4">
                        Continue Reading
                    </span>{' '}
                    →
                </Link>
            </div>
        </div>
    );
}
