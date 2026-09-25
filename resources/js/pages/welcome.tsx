import { Head } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import BlogFooter from '@/components/blog/blog-footer';
import BlogHeader from '@/components/blog/blog-header';
import CategoryFilter from '@/components/blog/category-filter';
import FeaturedPost from '@/components/blog/featured-post';
import PostCard from '@/components/blog/post-card';
import type { BlogPost } from '@/types/blog';

interface WelcomeProps {
    featuredPost: BlogPost | null;
    posts: BlogPost[];
    categories: string[];
}

export default function Welcome({
    featuredPost,
    posts,
    categories,
}: WelcomeProps) {
    const [activeCategory, setActiveCategory] = useState('All');
    const [search, setSearch] = useState('');

    const visiblePosts = useMemo(() => {
        return posts.filter((post) => {
            const matchesCategory =
                activeCategory === 'All' || post.category === activeCategory;
            const matchesSearch = post.title
                .toLowerCase()
                .includes(search.trim().toLowerCase());

            return matchesCategory && matchesSearch;
        });
    }, [posts, activeCategory, search]);

    return (
        <>
            <Head title="Venndor Blog" />

            <div className="flex min-h-screen flex-col bg-white font-brand">
                <BlogHeader />

                <main className="flex flex-1 flex-col gap-16 px-4 pb-16 sm:px-8 lg:gap-20 lg:px-20">
                    <section className="flex flex-col gap-8 pt-6 sm:gap-9 lg:pt-12">
                        <div
                            className="relative flex h-[180px] w-full items-end overflow-hidden rounded-t-3xl sm:h-[210px] lg:h-[236px]"
                            style={{
                                backgroundImage:
                                    "url('/assets/img/blog/hero-banner.jpg')",
                                backgroundSize: 'cover',
                                backgroundPosition: 'center',
                            }}
                        >
                            <div
                                className="absolute inset-0"
                                style={{
                                    backgroundImage:
                                        'linear-gradient(90deg, rgba(0, 0, 0, 0.32) 0%, rgba(0, 0, 0, 0.32) 100%), linear-gradient(-88.2deg, rgba(0, 0, 0, 0) 28.767%, rgba(0, 0, 0, 0.63) 61.514%, rgba(0, 0, 0, 0.9) 77.166%)',
                                }}
                            />
                            <div className="relative flex items-center gap-3 p-6 lg:p-[42px]">
                                <img
                                    src="/logo.svg"
                                    alt="Venndor"
                                    className="h-5 w-auto brightness-0 invert lg:h-[22px]"
                                />
                                <span className="text-2xl font-medium text-white lg:text-[27px]">
                                    BLOG
                                </span>
                            </div>
                        </div>

                        <p className="font-brand text-base text-brand-ink">
                            Your premier hub for expert strategies, planning
                            toolkits, and unforgettable stories from the
                            world&rsquo;s best events.
                        </p>

                        <CategoryFilter
                            categories={categories}
                            active={activeCategory}
                            onActiveChange={setActiveCategory}
                            search={search}
                            onSearchChange={setSearch}
                        />
                    </section>

                    {featuredPost && <FeaturedPost post={featuredPost} />}

                    <div className="border-t border-brand-stroke" />

                    {posts.length === 0 ? (
                        <p className="py-8 text-center font-brand text-base text-brand-muted">
                            {featuredPost
                                ? 'More headlines are on the way — check back soon.'
                                : 'No headlines published yet — check back soon.'}
                        </p>
                    ) : visiblePosts.length > 0 ? (
                        <div className="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
                            {visiblePosts.map((post) => (
                                <PostCard key={post.id} post={post} />
                            ))}
                        </div>
                    ) : (
                        <p className="py-8 text-center font-brand text-base text-brand-muted">
                            No headlines match “{search}” in{' '}
                            {activeCategory === 'All'
                                ? 'any category'
                                : activeCategory}
                            .
                        </p>
                    )}

                    <div className="border-t border-brand-stroke" />
                </main>

                <BlogFooter />
            </div>
        </>
    );
}
