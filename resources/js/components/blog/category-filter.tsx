import { Search } from 'lucide-react';
import { cn } from '@/lib/utils';

interface CategoryFilterProps {
    categories: string[];
    active: string;
    onActiveChange: (category: string) => void;
    search: string;
    onSearchChange: (value: string) => void;
}

export default function CategoryFilter({
    categories,
    active,
    onActiveChange,
    search,
    onSearchChange,
}: CategoryFilterProps) {
    const pillClasses = (isActive: boolean) =>
        cn(
            'shrink-0 rounded-lg border px-3 py-1 font-brand text-sm whitespace-nowrap transition-colors sm:px-6 sm:text-base',
            isActive
                ? 'border-brand bg-brand-tint text-brand-ink'
                : 'border-brand-border-light bg-brand-surface text-brand-ink hover:bg-brand-tint',
        );

    return (
        <div className="flex w-full items-center gap-2 border-b border-brand-border pt-2 pb-6 sm:gap-2.5">
            <span className="shrink-0 font-brand text-sm text-brand-muted sm:text-base">
                Categories:
            </span>

            <button
                type="button"
                onClick={() => onActiveChange('All')}
                className={pillClasses(active === 'All')}
            >
                All
            </button>

            <div className="no-scrollbar flex min-w-0 flex-1 items-center gap-2 overflow-x-auto sm:gap-2.5">
                {categories.map((category) => (
                    <button
                        key={category}
                        type="button"
                        onClick={() => onActiveChange(category)}
                        className={pillClasses(active === category)}
                    >
                        {category}
                    </button>
                ))}
            </div>

            <label className="flex w-[130px] shrink-0 items-center gap-2 rounded-3xl border border-brand-outline bg-white px-3 py-2 sm:w-[220px] sm:px-4 sm:py-3 md:w-[300px] lg:w-[406px]">
                <Search className="size-4 shrink-0 text-brand-outline sm:size-5" />
                <input
                    type="search"
                    value={search}
                    onChange={(event) => onSearchChange(event.target.value)}
                    placeholder="Search Headlines"
                    className="w-full min-w-0 font-brand text-sm text-brand-ink placeholder:text-brand-border-light focus:outline-none sm:text-base"
                />
            </label>
        </div>
    );
}
