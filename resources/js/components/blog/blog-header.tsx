import { Link } from '@inertiajs/react';
import { register } from '@/routes';

export default function BlogHeader() {
    return (
        <header className="flex h-16 w-full items-center justify-between border-b border-brand-stroke bg-white px-4 sm:h-20 sm:px-8 lg:px-20">
            <Link href="/" className="flex shrink-0 items-center">
                <img
                    src="/logo.svg"
                    alt="Venndor"
                    className="h-5 w-auto sm:h-6"
                />
            </Link>

            <Link
                href={register()}
                className="inline-flex shrink-0 items-center justify-center rounded-full bg-brand px-4 py-1.5 font-brand text-sm font-medium text-brand-tint transition-colors hover:bg-brand/90 sm:px-5 sm:py-2 sm:text-base"
            >
                Get Started
            </Link>
        </header>
    );
}
