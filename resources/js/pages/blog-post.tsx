import { Head, Link } from "@inertiajs/react";
import { useState } from "react";
import BlogFooter from "@/components/blog/blog-footer";
import BlogHeader from "@/components/blog/blog-header";
import PostCard from "@/components/blog/post-card";
import PostImage from "@/components/blog/post-image";
import ShareDialog from "@/components/blog/share-dialog";
import {
    InstagramIcon,
    LinkedInIcon,
    MoreIcon,
    XIcon,
} from "@/components/blog/share-icons";
import { useShare } from "@/components/blog/use-share";
import type { SharePlatform } from "@/components/blog/use-share";
import { home } from "@/routes";
import type { BlogPost, BlogPostDetail } from "@/types/blog";

interface BlogPostPageProps {
    post: BlogPostDetail;
    related: BlogPost[];
}

const SHARE_BUTTONS: {
    id: SharePlatform | null; // null = "More" (opens the dialog)
    label: string;
    Icon: typeof XIcon;
}[] = [
    { id: "x", label: "X", Icon: XIcon },
    { id: "linkedin", label: "LinkedIn", Icon: LinkedInIcon },
    { id: "instagram", label: "Instagram", Icon: InstagramIcon },
    { id: null, label: "More", Icon: MoreIcon },
];

export default function BlogPostPage({ post, related }: BlogPostPageProps) {
    const [shareOpen, setShareOpen] = useState(false);
    const { share, notice } = useShare(post);

    // X, LinkedIn and Instagram share directly; "More" opens the dialog.
    const handleShareClick = (platform: SharePlatform | null) => {
        if (platform) {
            void share(platform);
        } else {
            setShareOpen(true);
        }
    };

    return (
        <>
            <Head title={post.seoTitle}>
                {post.seoDescription && (
                    <meta name="description" content={post.seoDescription} />
                )}
                {/* og:* and twitter:* tags are rendered server-side in app.blade.php
                    (social crawlers don't run JavaScript). */}
            </Head>

            <div className="flex min-h-screen flex-col bg-white font-brand">
                <BlogHeader />

                <main className="flex flex-1 flex-col gap-4 md:gap-10 px-4  pb-20 sm:px-8 lg:px-20">
                    <div className="mx-auto flex w-full max-w-3xl flex-col gap-8">
                        <Link
                            href={home()}
                            className="pt-6 font-brand font-medium text-brand-ink lg:pt-10"
                        >
                            <span className="underline text-xs md:text-base decoration-solid underline-offset-4">
                                ← Back to Blog
                            </span>
                        </Link>

                        <div className="mt-4">
                            <h1 className="font-brand text-2xl font-bold text-brand md:text-5xl md:tracking-[-2%] md:leading-15">
                                {post.title}
                            </h1>
                        </div>
                    </div>

                    <article className="mx-auto flex w-full max-w-3xl flex-col gap-8">
                        <header className="flex flex-col gap-4">
                            <div className="flex items-start md:items-center justify-start flex-col md:flex-row md:justify-between gap-4 md:gap-0">
                                <div className="flex items-center gap-1">
                                    <div className="flex flex-col">
                                        {post.author && (
                                            <p className="flex-1 font-brand text-xs md:text-sm text-brand-subtle">
                                                By{" "}
                                                <span className="font-medium text-brand-ink">
                                                    {post.author}
                                                </span>
                                            </p>
                                        )}
                                        <p className="flex-1 font-brand text-xs md:text-sm text-brand-subtle">
                                            {[
                                                post.readTime,
                                                post.category,
                                                post.date,
                                            ]
                                                .filter(Boolean)
                                                .join(" • ")}
                                        </p>
                                    </div>
                                </div>

                                <div className="flex flex-col text-xs md:text-sm items-start md:items-center gap-1">
                                    <p>Share via</p>
                                    <div className="flex items-start md:items-center gap-1">
                                        {SHARE_BUTTONS.map(
                                            ({ id, label, Icon }) => (
                                                <button
                                                    key={label}
                                                    type="button"
                                                    aria-label={`Share on ${label}`}
                                                    aria-haspopup={
                                                        id
                                                            ? undefined
                                                            : "dialog"
                                                    }
                                                    onClick={() =>
                                                        handleShareClick(id)
                                                    }
                                                    className="cursor-pointer flex items-center justify-center p-1.5 rounded-md h-7 w-7 md:h-9 md:w-9 bg-[#F4F4F4] transition-colors hover:bg-brand-surface focus-visible:outline-2 focus-visible:outline-brand"
                                                >
                                                    <Icon className="h-5.5 w-5.5 md:h-6.25 md:w-6.25" />
                                                </button>
                                            ),
                                        )}
                                    </div>
                                    {notice && (
                                        <p
                                            role="status"
                                            aria-live="polite"
                                            className="max-w-56 text-xs text-brand-subtle md:text-right"
                                        >
                                            {notice}
                                        </p>
                                    )}
                                </div>
                            </div>

                            {post.excerpt && (
                                <p className="font-brand text-sm md:text-lg leading-8 text-brand-ink">
                                    {post.excerpt}
                                </p>
                            )}
                        </header>
                    </article>

                    <section className="mx-auto flex w-full max-w-6xl flex-col gap-8 pt-8">
                        <PostImage
                            src={post.image}
                            alt={post.title}
                            loading="eager"
                            className="h-60 w-full border-t-8 border-[#006B5E] rounded-b-3xl sm:h-90 lg:h-120"
                        />
                    </section>

                    <section className="mx-auto flex w-full max-w-3xl flex-col gap-8">
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
                    </section>

                    {related.length > 0 && (
                        <section className="mx-auto flex w-full max-w-6xl flex-col gap-8 border-t border-brand-stroke pt-12">
                            <h2 className="font-brand text-2xl font-medium text-brand">
                                More from Venndor
                            </h2>
                            <div className="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-2">
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

            <ShareDialog
                open={shareOpen}
                onOpenChange={setShareOpen}
                post={post}
            />
        </>
    );
}
