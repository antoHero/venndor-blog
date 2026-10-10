import { Head, Link } from "@inertiajs/react";
import BlogFooter from "@/components/blog/blog-footer";
import BlogHeader from "@/components/blog/blog-header";
import PostCard from "@/components/blog/post-card";
import PostImage from "@/components/blog/post-image";
import { home } from "@/routes";
import type { BlogPost, BlogPostDetail } from "@/types/blog";

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

                        <div className="mt-4">
                            <h1 className="font-brand text-[48px] leading-[60px] font-bold text-brand sm:text-[40px] sm:leading-[48px]">
                                {post.title}
                            </h1>
                        </div>
                    </div>

                    <article className="mx-auto flex w-full max-w-3xl flex-col gap-8">
                        <header className="flex flex-col gap-4">
                            <div className="flex items-center justify-between">
                                <div className="flex items-center gap-1">
                                    <div className="flex flex-col">
                                        <p className="flex-1 font-brand text-sm text-brand-subtle">
                                            {[
                                                post.readTime,
                                                post.category,
                                                post.date,
                                            ]
                                                .filter(Boolean)
                                                .join(" • ")}
                                        </p>
                                        {post.author && (
                                            <p className="font-brand text-sm text-brand-subtle">
                                                By{" "}
                                                <span className="font-medium text-brand-ink">
                                                    {post.author}
                                                </span>
                                            </p>
                                        )}
                                    </div>
                                    {post.tag && (
                                        <span className="flex shrink-0 items-center gap-1 font-brand text-sm font-medium text-brand-success">
                                            <span className="size-2 rounded-full bg-brand-success" />
                                            {post.tag}
                                        </span>
                                    )}
                                </div>

                                <div className="flex flex-col items-center gap-1">
                                    <p>Share via</p>
                                    <div className="flex items-center gap-1">
                                        <span className="cursor-pointer p-1.5 rounded-md h-9 w-9 bg-[#F4F4F4]">
                                            <svg
                                                width="24"
                                                height="25"
                                                viewBox="0 0 24 25"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    d="M12.9211 11.3722L18.5014 5.02002H17.1795L12.3321 10.5344L8.46328 5.02002H4L9.85162 13.3596L4 20.02H5.3219L10.4376 14.1954L14.5242 20.02H18.9875M5.799 5.99621H7.82981L17.1785 19.0918H15.1472"
                                                    fill="#423A3A"
                                                    stroke-width="0.4"
                                                    stroke="#423A3A"
                                                />
                                            </svg>
                                        </span>
                                        <span className="cursor-pointer p-1.5 rounded-md h-9 w-9 bg-[#F4F4F4]">
                                            <svg
                                                width="24"
                                                height="24"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    d="M5 1.25C3.48122 1.25 2.25 2.48122 2.25 4C2.25 5.51878 3.48122 6.75 5 6.75C6.51878 6.75 7.75 5.51878 7.75 4C7.75 2.48122 6.51878 1.25 5 1.25ZM3.75 4C3.75 3.30964 4.30964 2.75 5 2.75C5.69036 2.75 6.25 3.30964 6.25 4C6.25 4.69036 5.69036 5.25 5 5.25C4.30964 5.25 3.75 4.69036 3.75 4Z"
                                                    fill-rule="evenodd"
                                                    clip-rule="evenodd"
                                                    fill="#423A3A"
                                                />
                                                <path
                                                    d="M2.25 8C2.25 7.58579 2.58579 7.25 3 7.25H7C7.41421 7.25 7.75 7.58579 7.75 8V21C7.75 21.4142 7.41421 21.75 7 21.75H3C2.58579 21.75 2.25 21.4142 2.25 21V8ZM3.75 8.75V20.25H6.25V8.75H3.75Z"
                                                    fill-rule="evenodd"
                                                    clip-rule="evenodd"
                                                    fill="#423A3A"
                                                />
                                                <path
                                                    d="M9.25 8C9.25 7.58579 9.58579 7.25 10 7.25H14C14.4142 7.25 14.75 7.58579 14.75 8V8.43402L15.1853 8.24748C15.9336 7.92676 16.7339 7.72565 17.5433 7.65207C20.3182 7.3998 22.75 9.58038 22.75 12.3802V21C22.75 21.4142 22.4142 21.75 22 21.75H18C17.5858 21.75 17.25 21.4142 17.25 21V14C17.25 13.6685 17.1183 13.3505 16.8839 13.1161C16.6495 12.8817 16.3315 12.75 16 12.75C15.6685 12.75 15.3505 12.8817 15.1161 13.1161C14.8817 13.3505 14.75 13.6685 14.75 14V21C14.75 21.4142 14.4142 21.75 14 21.75H10C9.58579 21.75 9.25 21.4142 9.25 21V8ZM10.75 8.75V20.25H13.25V14C13.25 13.2707 13.5397 12.5712 14.0555 12.0555C14.5712 11.5397 15.2707 11.25 16 11.25C16.7293 11.25 17.4288 11.5397 17.9445 12.0555C18.4603 12.5712 18.75 13.2707 18.75 14V20.25H21.25V12.3802C21.25 10.4759 19.589 8.97227 17.6791 9.14591C17.025 9.20536 16.3784 9.36807 15.7762 9.6262L14.2954 10.2608C14.0637 10.3601 13.7976 10.3363 13.5871 10.1976C13.3767 10.0588 13.25 9.82354 13.25 9.57143V8.75H10.75Z"
                                                    fill-rule="evenodd"
                                                    clip-rule="evenodd"
                                                    fill="#423A3A"
                                                />
                                            </svg>
                                        </span>
                                        <span className="cursor-pointer p-1.5 rounded-md h-9 w-9 bg-[#F4F4F4]">
                                            <svg
                                                width="24"
                                                height="25"
                                                viewBox="0 0 24 25"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    d="M12 3.02002C9.55762 3.02002 9.2505 3.03127 8.29087 3.07402C7.33125 3.11902 6.67762 3.26977 6.105 3.49252C5.50407 3.71789 4.95991 4.07234 4.51088 4.53089C4.05232 4.97993 3.69787 5.52409 3.4725 6.12502C3.24975 6.69652 3.09787 7.35127 3.054 8.30752C3.01125 9.26939 3 9.5754 3 12.0211C3 14.4646 3.01125 14.7706 3.054 15.7303C3.099 16.6888 3.24975 17.3424 3.4725 17.915C3.70312 18.5068 4.01025 19.0085 4.51088 19.5091C5.01038 20.0098 5.51213 20.318 6.10388 20.5475C6.67763 20.7703 7.33013 20.9221 8.28863 20.966C9.24938 21.0088 9.55538 21.02 12 21.02C14.4446 21.02 14.7495 21.0088 15.7103 20.966C16.6676 20.921 17.3235 20.7703 17.8961 20.5475C18.4966 20.322 19.0404 19.9676 19.4891 19.5091C19.9897 19.0085 20.2969 18.5068 20.5275 17.915C20.7491 17.3424 20.901 16.6888 20.946 15.7303C20.9888 14.7706 21 14.4646 21 12.02C21 9.57539 20.9888 9.26939 20.946 8.30864C20.901 7.35127 20.7491 6.69652 20.5275 6.12502C20.3021 5.52409 19.9477 4.97993 19.4891 4.53089C19.0401 4.07234 18.4959 3.71789 17.895 3.49252C17.3212 3.26977 16.6665 3.11789 15.7091 3.07402C14.7484 3.03127 14.4435 3.02002 11.9978 3.02002H12ZM11.1934 4.64227H12.0011C14.4041 4.64227 14.6887 4.65014 15.6371 4.69402C16.5146 4.73339 16.9916 4.88077 17.3089 5.00339C17.7285 5.16652 18.0289 5.36227 18.3439 5.67727C18.6589 5.99227 18.8535 6.29152 19.0166 6.71227C19.1404 7.02839 19.2866 7.50539 19.326 8.38289C19.3699 9.33127 19.3789 9.61589 19.3789 12.0178C19.3789 14.4196 19.3699 14.7054 19.326 15.6538C19.2866 16.5313 19.1392 17.0071 19.0166 17.3244C18.8713 17.7147 18.6411 18.0678 18.3427 18.3583C18.0277 18.6733 17.7285 18.8679 17.3078 19.031C16.9928 19.1548 16.5157 19.301 15.6371 19.3415C14.6887 19.3843 14.4041 19.3944 12.0011 19.3944C9.59813 19.3944 9.31238 19.3843 8.364 19.3415C7.4865 19.301 7.01062 19.1548 6.69338 19.031C6.30281 18.8862 5.9493 18.6564 5.65837 18.3583C5.35943 18.0676 5.12887 17.714 4.98338 17.3233C4.86075 17.0071 4.71338 16.5301 4.674 15.6526C4.63125 14.7043 4.62225 14.4196 4.62225 12.0155C4.62225 9.61139 4.63125 9.32902 4.674 8.38064C4.7145 7.50314 4.86075 7.02614 4.9845 6.70889C5.14762 6.28927 5.34337 5.98889 5.65837 5.67389C5.97337 5.35889 6.27262 5.16427 6.69338 5.00114C7.01062 4.87739 7.4865 4.73114 8.364 4.69064C9.19425 4.65239 9.516 4.64114 11.1934 4.64002V4.64227ZM16.8049 6.13627C16.663 6.13627 16.5226 6.1642 16.3916 6.21848C16.2605 6.27275 16.1415 6.35231 16.0412 6.45259C15.9409 6.55288 15.8614 6.67194 15.8071 6.80297C15.7528 6.934 15.7249 7.07444 15.7249 7.21627C15.7249 7.3581 15.7528 7.49854 15.8071 7.62957C15.8614 7.7606 15.9409 7.87966 16.0412 7.97994C16.1415 8.08023 16.2605 8.15978 16.3916 8.21406C16.5226 8.26833 16.663 8.29627 16.8049 8.29627C17.0913 8.29627 17.366 8.18248 17.5686 7.97994C17.7711 7.77741 17.8849 7.5027 17.8849 7.21627C17.8849 6.92984 17.7711 6.65513 17.5686 6.45259C17.366 6.25005 17.0913 6.13627 16.8049 6.13627ZM12.0011 7.39852C11.3881 7.38896 10.7793 7.50144 10.2101 7.72943C9.64094 7.95742 9.12283 8.29635 8.68592 8.7265C8.24901 9.15664 7.90203 9.66941 7.6652 10.2349C7.42836 10.8005 7.30639 11.4075 7.30639 12.0206C7.30639 12.6337 7.42836 13.2407 7.6652 13.8062C7.90203 14.3718 8.24901 14.8845 8.68592 15.3147C9.12283 15.7448 9.64094 16.0837 10.2101 16.3117C10.7793 16.5397 11.3881 16.6522 12.0011 16.6426C13.2145 16.6237 14.3717 16.1284 15.2231 15.2637C16.0745 14.3989 16.5516 13.2341 16.5516 12.0206C16.5516 10.8071 16.0745 9.64223 15.2231 8.77749C14.3717 7.91274 13.2145 7.41745 12.0011 7.39852ZM12.0011 9.01964C12.3951 9.01964 12.7853 9.09725 13.1493 9.24803C13.5133 9.39882 13.8441 9.61982 14.1227 9.89843C14.4013 10.177 14.6223 10.5078 14.7731 10.8718C14.9239 11.2358 15.0015 11.626 15.0015 12.02C15.0015 12.414 14.9239 12.8042 14.7731 13.1682C14.6223 13.5322 14.4013 13.863 14.1227 14.1416C13.8441 14.4202 13.5133 14.6412 13.1493 14.792C12.7853 14.9428 12.3951 15.0204 12.0011 15.0204C11.2054 15.0204 10.4422 14.7043 9.87954 14.1416C9.31686 13.5789 9.00075 12.8158 9.00075 12.02C9.00075 11.2243 9.31686 10.4611 9.87954 9.89843C10.4422 9.33575 11.2054 9.01964 12.0011 9.01964Z"
                                                    fill="#423A3A"
                                                />
                                            </svg>
                                        </span>
                                        <span className="cursor-pointer p-1.5 rounded-md h-9 w-9 bg-[#F4F4F4]">
                                            <svg
                                                width="24"
                                                height="24"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    d="M18 12H18.01M12 12H12.01M6 12H6.01M13 12C13 12.5523 12.5523 13 12 13C11.4477 13 11 12.5523 11 12C11 11.4477 11.4477 11 12 11C12.5523 11 13 11.4477 13 12ZM19 12C19 12.5523 18.5523 13 18 13C17.4477 13 17 12.5523 17 12C17 11.4477 17.4477 11 18 11C18.5523 11 19 11.4477 19 12ZM7 12C7 12.5523 6.55228 13 6 13C5.44772 13 5 12.5523 5 12C5 11.4477 5.44772 11 6 11C6.55228 11 7 11.4477 7 12Z"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke="#423A3A"
                                                />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {post.excerpt && (
                                <p className="font-brand text-lg leading-8 text-brand-ink">
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
        </>
    );
}
