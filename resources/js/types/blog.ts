export interface BlogPost {
    id: string;
    slug: string;
    title: string;
    excerpt: string | null;
    image: string | null;
    date: string | null;
    readTime: string;
    category: string;
    tag?: string | null;
}

export interface BlogPostDetail extends BlogPost {
    body: string | null;
    author: string | null;
    tags: string[];
    seoTitle: string;
    seoDescription: string | null;
}
