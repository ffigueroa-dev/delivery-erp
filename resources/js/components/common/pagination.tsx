import { PaginationLinks } from "@/types";
import { Link } from "@inertiajs/react";
import { ChevronLeft, ChevronRight } from "lucide-react";

type PaginationProps = {
    links: PaginationLinks[];
};

export const Pagination = ({ links }: PaginationProps) => {
    const previous = links.find((link) =>
        link.label.includes("Previous"),
    );

    const next = links.find((link) =>
        link.label.includes("Next"),
    );

    const pages = links.filter(
        (link) =>
            !link.label.includes("Previous") &&
            !link.label.includes("Next"),
    );

    return (
        <div className="flex items-center justify-center gap-1">
            {previous?.url && (
                <Link
                    href={previous.url}
                    className="rounded-md p-2 text-gray-500 hover:bg-gray-100"
                >
                    <ChevronLeft className="size-5" />
                </Link>
            )}

            {pages.map((link, index) =>
                link.url ? (
                    <Link
                        key={index}
                        href={link.url}
                        className={`rounded-md px-3 py-2 text-sm font-medium ${
                            link.active
                                ? "bg-blue-100 text-blue-700"
                                : "text-gray-500 hover:bg-gray-100"
                        }`}
                        dangerouslySetInnerHTML={{
                            __html: link.label,
                        }}
                    />
                ) : (
                    <span
                        key={index}
                        className="px-3 py-2 text-sm text-gray-500"
                        dangerouslySetInnerHTML={{
                            __html: link.label,
                        }}
                    />
                ),
            )}

            {next?.url && (
                <Link
                    href={next.url}
                    className="rounded-md p-2 text-gray-500 hover:bg-gray-100"
                >
                    <ChevronRight className="size-5" />
                </Link>
            )}
        </div>
    );
};
