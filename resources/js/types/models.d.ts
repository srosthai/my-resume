/**
 * Shapes of the Eloquent models as they arrive in Inertia props.
 * Keep in sync with app/Models and the controller `select()` lists.
 */

export interface Timestamps {
    created_at: string | null;
    updated_at: string | null;
}

/** Owner fields available on public pages (User::publicColumns()). */
export interface PublicOwner {
    id: number;
    name: string;
    position: string | null;
    description: string | null;
    image: string | null;
}

/** Owner fields on the resume page (adds contact details). */
export interface ResumeOwner extends PublicOwner {
    email: string;
    phone: string | null;
    address: string | null;
}

/** Full profile record as edited in the admin "Me" pages. */
export interface UserProfile extends PublicOwner, Timestamps {
    email: string;
    dob: string | null;
    phone: string | null;
    address: string | null;
    email_verified_at?: string | null;
    is_owner?: boolean;
}

/** Minimal author relation (id, name, image) loaded on notes and feeds. */
export interface Author {
    id: number;
    name: string;
    image: string | null;
}

export interface AboutMe extends Timestamps {
    id: number;
    title: string | null;
    description: string | null;
    location: string | null;
    year_experience: string | null;
    focus_on: string | null;
}

export interface WorkExperience extends Timestamps {
    id: number;
    title: string | null;
    position: string | null;
    company: string | null;
    description: string | null;
    from: string | null;
    to: string | null;
}

export interface Education extends Timestamps {
    id: number;
    title: string | null;
    major: string | null;
    institution: string | null;
    description: string | null;
    from: string | null;
    to: string | null;
}

export interface TechStack extends Timestamps {
    id: number;
    name: string | null;
    logo: string | null;
    type: string | null;
    description: string | null;
}

export interface ProjectType extends Timestamps {
    id: number;
    name: string | null;
    projects_count?: number;
}

export type ProjectStatus = 'processing' | 'completed';

/** Links are stored as [{ Github: 'https://...' }, { View: 'https://...' }]. */
export type ProjectLink = Record<string, string>;

export interface Project extends Timestamps {
    id: number;
    title: string | null;
    slug: string | null;
    description: string | null;
    image: string | null;
    project_type_id: number | null;
    project_type?: ProjectType | null;
    technologies: string[] | null;
    created_date: string | null;
    status: ProjectStatus;
    links: ProjectLink[] | null;
}

export interface PopularSong extends Timestamps {
    id: number;
    title: string;
    artist: string;
    url: string;
    duration: number;
}

/** Shape returned by GET /api/popular-songs for the music player. */
export interface PlayerSong {
    id: number;
    title: string;
    artist: string;
    src: string;
    duration: number;
}

export type PublishStatus = 'draft' | 'published' | 'archived';

export interface NoteStep {
    title: string;
    description: string;
    commands: string[];
}

export interface NoteContent {
    overview: string;
    requirements: string[];
    steps: NoteStep[];
}

export interface Note extends Timestamps {
    id: number;
    title: string;
    category: string;
    description: string;
    tags: string[] | null;
    content: NoteContent;
    slug: string;
    status: PublishStatus;
    user_id: number | null;
    user?: Author | null;
    views: number;
    is_featured: boolean;
    published_at: string | null;
}

export type FeedVisibility = 'public' | 'private';

export interface Feed extends Timestamps {
    id: number;
    title: string | null;
    body: string;
    images: string[] | null;
    location: string | null;
    mood: string | null;
    activity_type: string | null;
    tags: string[] | null;
    slug: string;
    visibility: FeedVisibility;
    status: PublishStatus;
    user_id: number | null;
    user?: Author | null;
    likes_count: number;
    /** True when this visitor's IP already liked the feed. Server is the source of truth. */
    liked?: boolean;
    views: number;
    is_pinned: boolean;
    published_at: string | null;
}

/** Laravel paginator payload as sent by ->paginate(). */
export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
    first_page_url: string;
    last_page_url: string;
    next_page_url: string | null;
    prev_page_url: string | null;
}

/** Empty-object/array placeholder Laravel sends when a nullable "first()" record is absent. */
export type AboutMeProp = AboutMe | [];

/** Project with the legacy free-text `tech_stack` column still read as a fallback on portfolio pages. */
export interface LegacyProject extends Project {
    tech_stack?: string | null;
}

/** Neighbouring project used for prev/next navigation (select id, title). */
export type ProjectNeighbour = Pick<Project, 'id' | 'title' | 'slug'>;

/**
 * Optional fields the resume template renders when present. They are not
 * persisted by the current migrations, so they stay optional here.
 */
export interface ResumeWorkExperience extends WorkExperience {
    responsibilities?: string | null;
}

export interface ResumeEducation extends Education {
    degree?: string | null;
    gpa?: string | null;
}

/**
 * Inertia's `useForm` data must be index-signature compatible, which interfaces are not.
 * These mapped copies of the note content shapes are what the note create/edit forms use.
 */
export type NoteStepForm = { [K in keyof NoteStep]: NoteStep[K] };
export type NoteContentForm = { [K in keyof NoteContent]: K extends 'steps' ? NoteStepForm[] : NoteContent[K] };
