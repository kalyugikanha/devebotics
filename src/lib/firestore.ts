import {
  collection, doc, getDoc, getDocs, addDoc, setDoc, updateDoc, deleteDoc,
  onSnapshot, query, orderBy, where, Timestamp, DocumentData
} from 'firebase/firestore';
import { db } from './firebase';

// ─── Types ────────────────────────────────────────────────────────────────────

export interface NavItem {
  id?: string;
  label: string;
  type: 'link' | 'mega' | 'simple';
  href?: string;
  order: number;
  children?: NavChild[];
}

export interface NavChild {
  label: string;
  href: string;
  order: number;
}

export interface BlogPost {
  id?: string;
  title: string;
  slug: string;
  excerpt: string;
  body: string;
  category: string;
  tags: string[];
  coverImage: string;
  author: string;
  status: 'draft' | 'published';
  createdAt: Timestamp | Date;
  updatedAt: Timestamp | Date;
  readTime?: string;
}

export interface BlogCategory {
  id?: string;
  name: string;
  order: number;
}

export interface FooterData {
  id?: string;
  columns: FooterColumn[];
  contact: {
    email: string;
    phone: string;
    address: string;
  };
  socials: FooterSocial[];
  copyright: string;
  tagline: string;
}

export interface FooterColumn {
  heading: string;
  links: { label: string; href: string }[];
}

export interface FooterSocial {
  label: string;
  href: string;
}

export interface ContactDetails {
  id?: string;
  email: string;
  whatsapp: string;
  responseTime: string;
  officeIndia: {
    city: string;
    address: string;
    phone: string;
    email: string;
    mapLink: string;
  };
  officeUSA: {
    city: string;
    address: string;
    phone: string;
    email: string;
    mapLink: string;
  };
}

// ─── Navigation ───────────────────────────────────────────────────────────────

export const getNavItems = async (): Promise<NavItem[]> => {
  const snap = await getDocs(query(collection(db, 'navigation'), orderBy('order')));
  return snap.docs.map(d => ({ id: d.id, ...d.data() } as NavItem));
};

export const saveNavItem = async (item: NavItem) => {
  if (item.id) {
    const { id, ...data } = item;
    await updateDoc(doc(db, 'navigation', id), data as DocumentData);
  } else {
    await addDoc(collection(db, 'navigation'), item);
  }
};

export const deleteNavItem = async (id: string) => {
  await deleteDoc(doc(db, 'navigation', id));
};

// ─── Blogs ────────────────────────────────────────────────────────────────────

export const getBlogs = async (status?: 'draft' | 'published'): Promise<BlogPost[]> => {
  let q = status
    ? query(collection(db, 'blogs'), where('status', '==', status), orderBy('createdAt', 'desc'))
    : query(collection(db, 'blogs'), orderBy('createdAt', 'desc'));
  const snap = await getDocs(q);
  return snap.docs.map(d => ({ id: d.id, ...d.data() } as BlogPost));
};

export const getBlogBySlug = async (slug: string): Promise<BlogPost | null> => {
  const q = query(collection(db, 'blogs'), where('slug', '==', slug));
  const snap = await getDocs(q);
  if (snap.empty) return null;
  const d = snap.docs[0];
  return { id: d.id, ...d.data() } as BlogPost;
};

export const saveBlog = async (blog: BlogPost): Promise<string> => {
  const now = Timestamp.now();
  if (blog.id) {
    const { id, ...data } = blog;
    await updateDoc(doc(db, 'blogs', id), { ...data, updatedAt: now });
    return id;
  } else {
    const ref = await addDoc(collection(db, 'blogs'), { ...blog, createdAt: now, updatedAt: now });
    return ref.id;
  }
};

export const deleteBlog = async (id: string) => {
  await deleteDoc(doc(db, 'blogs', id));
};

// ─── Blog Categories ──────────────────────────────────────────────────────────

export const getBlogCategories = async (): Promise<BlogCategory[]> => {
  const snap = await getDocs(query(collection(db, 'blogCategories'), orderBy('order')));
  return snap.docs.map(d => ({ id: d.id, ...d.data() } as BlogCategory));
};

export const saveBlogCategory = async (cat: BlogCategory) => {
  if (cat.id) {
    const { id, ...data } = cat;
    await updateDoc(doc(db, 'blogCategories', id), data as DocumentData);
  } else {
    await addDoc(collection(db, 'blogCategories'), cat);
  }
};

export const deleteBlogCategory = async (id: string) => {
  await deleteDoc(doc(db, 'blogCategories', id));
};

// ─── Footer ───────────────────────────────────────────────────────────────────

export const getFooterData = async (): Promise<FooterData | null> => {
  const snap = await getDoc(doc(db, 'siteConfig', 'footer'));
  if (!snap.exists()) return null;
  return snap.data() as FooterData;
};

export const saveFooterData = async (data: FooterData) => {
  await setDoc(doc(db, 'siteConfig', 'footer'), data);
};

// ─── Contact Details ──────────────────────────────────────────────────────────

export const getContactDetails = async (): Promise<ContactDetails | null> => {
  const snap = await getDoc(doc(db, 'siteConfig', 'contact'));
  if (!snap.exists()) return null;
  return snap.data() as ContactDetails;
};

export const saveContactDetails = async (data: ContactDetails) => {
  await setDoc(doc(db, 'siteConfig', 'contact'), data);
};
