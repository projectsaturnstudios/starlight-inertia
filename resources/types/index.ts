export interface NavPage {
    id: number | string;
    title: string;
    href: string;
}

export interface NavNode {
    id: number | string;
    title: string;
    url?: string | null;
    collapsed?: boolean;
    pages: NavPage[];
    children: NavNode[];
}

export interface TocEntry {
    depth: number;
    slug: string;
    text: string;
}

export interface SearchResult {
    title: string;
    href: string;
    section?: string;
    snippet?: string;
}

export interface PageLink {
    title: string;
    href: string;
}
