import { Head, Link } from '@inertiajs/react';
import BlogFooter from '@/components/blog/blog-footer';
import BlogHeader from '@/components/blog/blog-header';
import PostCard from '@/components/blog/post-card';
import PostImage from '@/components/blog/post-image';
import { home } from '@/routes';
import type { BlogPost, BlogPostDetail } from '@/types/blog';

interface BlogPostPageProps {
    post: BlogPostDetail;
    related: BlogPost[];
}

export default function BlogPostPage({ post, related }: BlogPostPageProps) {
    return (
        <>
            <Head title={post.seoTitle}>
                {post.seoDescription && (
                    <meta name="description" content={post.seoDescription} />
                )}
                <meta property="og:type" content="article" />
                <meta property="og:title" content={post.seoTitle} />
                {post.seoDescription && (
                    <meta
                        property="og:description"
                        content={post.seoDescription}
                    />
                )}
                {post.image && (
                    <meta property="og:image" content={post.image} />
                )}
            </Head>

            <div className="flex min-h-screen flex-col bg-white font-brand">
                <BlogHeader />

                <main className="flex flex-1 flex-col gap-10 px-4 pb-20 sm:px-8 lg:px-20">
                    <div className="mx-auto flex w-full max-w-3xl flex-col gap-8">
                        <Link
                            href={home()}
                            className="pt-6 font-brand text-base font-medium text-brand-ink lg:pt-10"
                        >
                            <span className="underline decoration-solid underline-offset-4">
                                ← Back to Blog
                            </span>
                        </Link>
                    </div>

                    <article className="mx-auto flex w-full max-w-3xl flex-col gap-8">
                        <header className="flex flex-col gap-4">
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

                            <h1 className="font-brand text-3xl leading-tight font-medium text-brand sm:text-[40px] sm:leading-[48px]">
                                {post.title}
                            </h1>

                            {post.excerpt && (
                                <p className="font-brand text-lg leading-8 text-brand-ink">
                                    {post.excerpt}
                                </p>
                            )}

                            {post.author && (
                                <p className="font-brand text-sm text-brand-subtle">
                                    By{' '}
                                    <span className="font-medium text-brand-ink">
                                        {post.author}
                                    </span>
                                </p>
                            )}
                        </header>

                        <PostImage
                            src={post.image}
                            alt={post.title}
                            loading="eager"
                            className="h-[240px] w-full rounded-3xl sm:h-[360px] lg:h-[480px]"
                        />

                        {post.body && (
                            <div
                                className="font-brand text-base leading-7 text-brand-ink [&_a]:text-brand [&_a]:underline [&_a]:underline-offset-4 [&_blockquote]:my-6 [&_blockquote]:border-l-4 [&_blockquote]:border-brand-tint [&_blockquote]:pl-4 [&_blockquote]:text-brand-subtle [&_blockquote]:italic [&_h2]:mt-8 [&_h2]:mb-3 [&_h2]:font-brand [&_h2]:text-2xl [&_h2]:font-medium [&_h2]:text-brand [&_h3]:mt-6 [&_h3]:mb-2 [&_h3]:font-brand [&_h3]:text-xl [&_h3]:font-medium [&_h3]:text-brand [&_img]:my-6 [&_img]:rounded-2xl [&_ol]:mb-4 [&_ol]:list-decimal [&_ol]:space-y-1 [&_ol]:pl-6 [&_p]:mb-4 [&_strong]:font-medium [&_strong]:text-brand [&_ul]:mb-4 [&_ul]:list-disc [&_ul]:space-y-1 [&_ul]:pl-6"
                                dangerouslySetInnerHTML={{ __html: post.body }}
                            />
                        )}

                        {post.tags.length > 0 && (
                            <div className="flex flex-wrap items-center gap-2 border-t border-brand-stroke pt-6">
                                {post.tags.map((tag) => (
                                    <span
                                        key={tag}
                                        className="rounded-lg border border-brand-border-light bg-brand-surface px-4 py-1 font-brand text-sm text-brand-ink"
                                    >
                                        {tag}
                                    </span>
                                ))}
                            </div>
                        )}
                    </article>

                    {related.length > 0 && (
                        <section className="mx-auto flex w-full max-w-6xl flex-col gap-8 border-t border-brand-stroke pt-12">
                            <h2 className="font-brand text-2xl font-medium text-brand">
                                More from Venndor
                            </h2>
                            <div className="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
                                {related.map((relatedPost) => (
                                    <PostCard
                                        key={relatedPost.id}
                                        post={relatedPost}
                                    />
                                ))}
                            </div>
                        </section>
                    )}
                </main>

                <BlogFooter />
            </div>
        </>
    );
}
