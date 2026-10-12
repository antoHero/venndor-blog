import { useEffect } from "react";
import ShareCard from "@/components/blog/share-card";
import {
    CloseIcon,
    FacebookIcon,
    InstagramIcon,
    LinkedInIcon,
    LinkIcon,
    MailIcon,
    MediumIcon,
    WhatsAppIcon,
    XIcon,
} from "@/components/blog/share-icons";
import { useShare } from "@/components/blog/use-share";
import type { SharePlatform } from "@/components/blog/use-share";
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogTitle,
} from "@/components/ui/dialog";
import type { BlogPostDetail } from "@/types/blog";

interface ShareDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    post: Pick<BlogPostDetail, "title" | "excerpt" | "image">;
}

const PLATFORMS: {
    id: SharePlatform;
    label: string;
    Icon: typeof XIcon;
}[] = [
    { id: "x", label: "X", Icon: XIcon },
    { id: "linkedin", label: "LinkedIn", Icon: LinkedInIcon },
    { id: "instagram", label: "Instagram", Icon: InstagramIcon },
    { id: "whatsapp", label: "WhatsApp", Icon: WhatsAppIcon },
    { id: "email", label: "Email", Icon: MailIcon },
    { id: "facebook", label: "Facebook", Icon: FacebookIcon },
    { id: "medium", label: "Medium", Icon: MediumIcon },
];

export default function ShareDialog({ open, onOpenChange, post }: ShareDialogProps) {
    const { share, copyLink, copied, notice, reset } = useShare(post);

    useEffect(() => {
        if (!open) reset();
    }, [open, reset]);

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            {/* [&>button]:hidden removes shadcn's built-in close button; we render our own in the header */}
            <DialogContent className="w-[calc(100%-2rem)] max-w-[640px] gap-0 rounded-3xl border border-brand-stroke bg-white p-0 shadow-xl sm:max-w-[640px] [&>button]:hidden">
                {/* Header: only in the dialog, never in the shared card */}
                <div className="flex items-center justify-between px-5 pt-4 pb-2">
                    <DialogTitle className="font-brand text-base font-medium text-brand">
                        Share
                    </DialogTitle>
                    <DialogDescription className="sr-only">
                        Share this article and preview how it will look.
                    </DialogDescription>
                    <DialogClose
                        aria-label="Close"
                        className="cursor-pointer flex size-8 items-center justify-center rounded-md text-brand-ink hover:bg-brand-surface focus-visible:outline-2 focus-visible:outline-brand"
                    >
                        <CloseIcon className="size-6" />
                    </DialogClose>
                </div>

                <div className="px-5">
                    <ShareCard post={post} />
                </div>

                <div className="mx-5 mt-6 border-t border-brand-stroke" />

                <div className="flex flex-col gap-3 px-5 pt-5 pb-5">
                    <div className="flex flex-wrap items-center justify-between gap-4">
                        <button
                            type="button"
                            onClick={copyLink}
                            className="flex h-10 cursor-pointer items-center gap-2 rounded-full bg-[#006B5E] px-5 font-brand text-sm font-medium text-white transition-colors hover:bg-[#005A4F] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand"
                        >
                            <LinkIcon className="size-5" />
                            {copied ? "Link copied" : "Copy Link"}
                        </button>

                        <div className="flex flex-wrap items-center gap-2">
                            {PLATFORMS.map(({ id, label, Icon }) => (
                                <button
                                    key={id}
                                    type="button"
                                    aria-label={`Share on ${label}`}
                                    title={`Share on ${label}`}
                                    onClick={() => share(id)}
                                    className="flex size-9 cursor-pointer items-center justify-center rounded-full bg-[#F4F4F4] transition-colors hover:bg-brand-surface focus-visible:outline-2 focus-visible:outline-brand"
                                >
                                    <Icon className="size-5" />
                                </button>
                            ))}
                        </div>
                    </div>

                    <p role="status" aria-live="polite" className="min-h-4 font-brand text-xs text-brand-subtle">
                        {notice}
                    </p>
                </div>
            </DialogContent>
        </Dialog>
    );
}
