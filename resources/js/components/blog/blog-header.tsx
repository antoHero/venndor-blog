import { Link, usePage } from '@inertiajs/react';
import { home } from '@/routes';

export default function BlogHeader() {
    const venndorUrl = usePage().props.venndor_url;
    console.log(venndorUrl);
    return (
        <header className="flex h-16 w-full items-center justify-between border-b border-brand-stroke bg-white px-4 sm:h-20 sm:px-8 lg:px-20">
            <Link href={home()} className="flex shrink-0 items-center">
                <img
                    src="/logo.svg"
                    alt="Venndor"
                    className="h-5 w-auto sm:h-6"
                />
            </Link>

            {/* Signs up for the wider Venndor platform, not this blog —
                that product lives outside this codebase, so there's no
                internal route to link to yet. */}
            <a
                href={`${venndorUrl}/login`}
                target="_blank"
                className="inline-flex shrink-0 items-center justify-center rounded-full bg-brand px-4 py-1.5 font-brand text-sm font-medium text-brand-tint transition-colors hover:bg-brand/90 sm:px-5 sm:py-2 sm:text-base"
            >
                Get Started
            </a>
        </header>
    );
}
