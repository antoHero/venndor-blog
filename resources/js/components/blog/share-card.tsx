import PostImage from "@/components/blog/post-image";
import { cn } from "@/lib/utils";
import type { BlogPostDetail } from "@/types/blog";

interface ShareCardProps {
    post: Pick<BlogPostDetail, "title" | "excerpt" | "image">;
    className?: string;
}

/**
 * The "shared article" card. It has NO header (no "Share" label / close button),
 * so it can be reused as-is anywhere the post preview is rendered.
 */
export default function ShareCard({ post, className }: ShareCardProps) {
    return (
        <div className={cn("flex flex-col gap-5 font-brand", className)}>
            <PostImage
                src={post.image}
                alt={post.title}
                loading="eager"
                className="h-44 w-full rounded-b-3xl border-t-8 border-[#006B5E] sm:h-60"
            />

            <div className="flex flex-col gap-3 px-3">
                <h3 className="font-brand text-xl font-medium text-brand sm:text-2xl">
                    {post.title}
                </h3>

                {post.excerpt && (
                    <p className="line-clamp-2 font-regular text-sm leading-6 text-brand-ink">
                        {post.excerpt}
                    </p>
                )}

                <div className="flex items-center gap-2 text-sm text-brand">
                    {/*<span className="flex size-5 items-center justify-center rounded-[5px] bg-brand-surface text-[10px] font-bold text-brand">
                        V
                    </span>*/}
                    {/*<img
                        src="/logo.svg"
                        alt="Venndor"
                        className="flex size-5 items-center justify-center rounded-[5px] bg-brand-surface text-[10px] font-bold text-brand h-5 w-auto brightness-0 invert lg:h-[22px]"
                    />*/}
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M11.5062 8.93404C11.5062 8.04132 10.7826 7.31763 9.88984 7.31763C8.99713 7.31763 8.27344 8.04132 8.27344 8.93404C8.27344 9.82677 8.99713 10.5505 9.88984 10.5505C10.7826 10.5505 11.5062 9.82677 11.5062 8.93404Z" fill="#FED900"/>
                    <path d="M18 5.23412C18.0003 4.54668 17.8651 3.86591 17.6022 3.23074C17.3393 2.59557 16.9538 2.01844 16.4677 1.53234C15.9816 1.04625 15.4045 0.660718 14.7693 0.39779C14.1341 0.134861 13.4534 -0.000310734 12.7659 5.36369e-07H5.23455C4.54712 -0.000186141 3.86641 0.135068 3.23127 0.398035C2.59613 0.661002 2.01903 1.04653 1.53292 1.53259C1.04682 2.01866 0.661238 2.59573 0.398216 3.23085C0.135193 3.86597 -0.000123932 4.54669 5.36315e-07 5.23412V8.66211L0.00426774 8.66637V8.67016C0.00313711 8.67016 0.00204895 8.67061 0.00124948 8.67141C0.000449999 8.67221 5.36315e-07 8.67329 5.36315e-07 8.67442V12.6329C-0.000310732 13.3204 0.134872 14.0013 0.397822 14.6365C0.660773 15.2717 1.04634 15.8489 1.53247 16.335C2.01861 16.8212 2.59578 17.2067 3.23101 17.4697C3.86623 17.7326 4.54705 17.8678 5.23455 17.8675H12.7659C13.4534 17.8678 14.1342 17.7326 14.7694 17.4697C15.4046 17.2067 15.9817 16.8212 16.4678 16.335C16.9539 15.8489 17.3395 15.2717 17.6024 14.6365C17.8652 14.0012 18.0004 13.3204 18 12.6329V8.67442C18 8.67016 18 8.66637 17.9957 8.67016C18 8.67016 18 8.66637 18 8.66211V5.23412ZM13.169 12.2014C13.1697 12.2275 13.1683 12.2537 13.1648 12.2796C13.1464 12.8533 12.9103 13.3984 12.5044 13.8043C12.0986 14.2102 11.5534 14.4463 10.9797 14.4646C10.9538 14.4682 10.9277 14.4696 10.9016 14.4689H7.0994C7.07327 14.4696 7.04714 14.4682 7.02125 14.4646C6.43414 14.4447 5.8777 14.1976 5.46927 13.7754C5.06083 13.3532 4.83233 12.7889 4.83193 12.2014V5.66469C4.83243 5.06347 5.07149 4.48702 5.49662 4.06189C5.92174 3.63676 6.49818 3.3977 7.0994 3.3972H10.9016C11.489 3.3976 12.0533 3.62611 12.4755 4.03455C12.8978 4.44298 13.1449 4.99942 13.1648 5.58653C13.1683 5.61242 13.1697 5.63856 13.169 5.66469V12.2014Z" fill="#346768"/>
                    </svg>

                    <span>Venndor Blog</span>
                </div>
            </div>
        </div>
    );
}
