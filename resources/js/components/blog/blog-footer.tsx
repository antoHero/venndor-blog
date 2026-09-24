import { ChevronUp } from 'lucide-react';
import {
    FacebookIcon,
    InstagramIcon,
    XIcon,
} from '@/components/blog/social-icons';

function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

export default function BlogFooter() {
    return (
        <footer className="flex w-full flex-col gap-10 bg-brand px-4 py-10 sm:px-8 lg:gap-[50px] lg:px-20 lg:py-[50px]">
            <div className="flex flex-wrap items-center justify-between gap-6">
                <img
                    src="/logo.svg"
                    alt="Venndor"
                    className="h-6 w-auto brightness-0 invert"
                />

                <button
                    type="button"
                    onClick={scrollToTop}
                    className="flex items-center gap-4 rounded-full border-[0.4px] border-brand-border bg-white py-2 pr-2 pl-6 font-brand text-base text-brand transition-opacity hover:opacity-90"
                >
                    Back to Top
                    <span className="flex size-10 items-center justify-center rounded-full bg-brand-surface-alt">
                        <ChevronUp className="size-6 text-brand-ink" />
                    </span>
                </button>
            </div>

            <div className="flex flex-col items-start justify-between gap-6 border-t-[0.4px] border-brand-light/60 pt-8 sm:flex-row sm:items-center">
                <p className="font-brand text-base text-white">
                    © {new Date().getFullYear()} Venndor. All rights reserved.
                </p>

                <div className="flex items-center gap-1.5">
                    <a
                        href="https://x.com/venndor"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Venndor on X"
                        className="flex items-center justify-center rounded-full bg-white p-2.5 text-brand transition-opacity hover:opacity-90"
                    >
                        <XIcon className="size-6" />
                    </a>
                    <a
                        href="https://facebook.com/venndor"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Venndor on Facebook"
                        className="flex items-center justify-center rounded-full bg-white p-3 text-brand transition-opacity hover:opacity-90"
                    >
                        <FacebookIcon className="size-5" />
                    </a>
                    <a
                        href="https://instagram.com/venndor"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Venndor on Instagram"
                        className="flex items-center justify-center rounded-full bg-white p-2.5 text-brand transition-opacity hover:opacity-90"
                    >
                        <InstagramIcon className="size-6" />
                    </a>
                </div>
            </div>
        </footer>
    );
}
