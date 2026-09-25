import { Link } from '@inertiajs/react';
import PostImage from '@/components/blog/post-image';
import posts from '@/routes/posts';
import type { BlogPost } from '@/types/blog';

interface PostCardProps {
    post: BlogPost;
}

export default function PostCard({ post }: PostCardProps) {
    const href = posts.show(post.slug);

    return (
        <article className="flex flex-1 flex-col gap-3">
            <Link
                href={href}
                className="relative block h-[200px] w-full overflow-hidden rounded-t-3xl"
            >
                <PostImage
                    src={post.image}
                    alt={post.title}
                    className="size-full"
                />
                <div className="absolute inset-0 bg-linear-to-b from-black/0 from-62% to-black/40" />
            </Link>

            <div className="flex flex-col gap-6 px-3">
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
                    <h3 className="font-brand text-xl font-medium text-brand sm:text-2xl">
                        <Link href={href} className="hover:underline">
                            {post.title}
                        </Link>
                    </h3>
                    {post.excerpt && (
                        <p className="line-clamp-2 font-brand text-sm text-brand-ink">
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
        </article>
    );
}
