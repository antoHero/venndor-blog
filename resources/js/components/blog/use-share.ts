import { useCallback, useEffect, useRef, useState } from "react";

export type SharePlatform =
    | "x"
    | "linkedin"
    | "instagram"
    | "whatsapp"
    | "email"
    | "facebook"
    | "medium";

interface ShareContent {
    title: string;
    excerpt?: string | null;
}

const enc = encodeURIComponent;

/** Platforms with a real web share intent. Instagram + Medium have none. */
function buildShareLink(platform: SharePlatform, url: string, title: string) {
    switch (platform) {
        case "x":
            return `https://twitter.com/intent/tweet?url=${enc(url)}&text=${enc(title)}`;
        case "linkedin":
            return `https://www.linkedin.com/sharing/share-offsite/?url=${enc(url)}`;
        case "facebook":
            return `https://www.facebook.com/sharer/sharer.php?u=${enc(url)}`;
        case "whatsapp":
            return `https://wa.me/?text=${enc(`${title} ${url}`)}`;
        case "email":
            return `mailto:?subject=${enc(title)}&body=${enc(`${title}\n\n${url}`)}`;
        default:
            return null;
    }
}

export function useShare({ title, excerpt }: ShareContent) {
    const [copied, setCopied] = useState(false);
    const [notice, setNotice] = useState("");
    const noticeTimer = useRef<ReturnType<typeof setTimeout> | undefined>(undefined);
    const copiedTimer = useRef<ReturnType<typeof setTimeout> | undefined>(undefined);

    useEffect(
        () => () => {
            clearTimeout(noticeTimer.current);
            clearTimeout(copiedTimer.current);
        },
        [],
    );

    // Read at click time so it's always the current, clean article URL (no hash).
    const getUrl = () => window.location.href.split("#")[0];

    const flash = useCallback((message: string) => {
        setNotice(message);
        clearTimeout(noticeTimer.current);
        noticeTimer.current = setTimeout(() => setNotice(""), 4000);
    }, []);

    const reset = useCallback(() => {
        setCopied(false);
        setNotice("");
    }, []);

    const copyLink = useCallback(async () => {
        try {
            await navigator.clipboard.writeText(getUrl());
            setCopied(true);
            clearTimeout(copiedTimer.current);
            copiedTimer.current = setTimeout(() => setCopied(false), 2000);
            return true;
        } catch {
            flash("Couldn't copy automatically. Copy the link from your address bar.");
            return false;
        }
    }, [flash]);

    const share = useCallback(
        async (platform: SharePlatform) => {
            const url = getUrl();
            const link = buildShareLink(platform, url, title);

            if (link) {
                if (link.startsWith("mailto:")) {
                    window.location.href = link;
                } else {
                    window.open(link, "_blank", "noopener,noreferrer,width=600,height=640");
                }
                return;
            }

            // Instagram / Medium: use the native share sheet when available,
            // otherwise copy the link and tell the reader what to do next.
            if (typeof navigator.share === "function") {
                try {
                    await navigator.share({ title, text: excerpt ?? "", url });
                } catch {
                    // reader dismissed the sheet
                }
                return;
            }

            if (await copyLink()) {
                flash(
                    platform === "instagram"
                        ? "Link copied. Paste it into your Instagram story or bio."
                        : "Link copied. Paste it into a new Medium story.",
                );
            }
        },
        [title, excerpt, copyLink, flash],
    );

    return { share, copyLink, copied, notice, reset };
}
