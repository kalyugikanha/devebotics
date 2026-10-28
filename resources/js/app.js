/**
 * Softec Ai Solution — public site bundle.
 *
 * Uses Alpine's CSP build so the site runs under a strict Content Security
 * Policy (no 'unsafe-eval'). All interactive behaviour is registered as
 * Alpine.data() components; templates only reference properties/methods.
 */
import Alpine from '@alpinejs/csp';
import focus from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';

import siteHeader from './components/site-header';
import themeToggle from './components/theme-toggle';
import announcement from './components/announcement';
import cookieConsent from './components/cookie-consent';
import tabs from './components/tabs';
import carousel from './components/carousel';
import autoSubmit from './components/auto-submit';
import formState from './components/form-state';
import fileInput from './components/file-input';
import shareLinks from './components/share-links';
import tableOfContents from './components/table-of-contents';
import motionToggle from './components/motion-toggle';
import { initMotion } from './motion';
import whatsappChat from './components/whatsapp-chat';

Alpine.plugin(focus);
Alpine.plugin(collapse);

Alpine.data('siteHeader', siteHeader);
Alpine.data('themeToggle', themeToggle);
Alpine.data('announcement', announcement);
Alpine.data('cookieConsent', cookieConsent);
Alpine.data('tabs', tabs);
Alpine.data('carousel', carousel);
Alpine.data('autoSubmit', autoSubmit);
Alpine.data('formState', formState);
Alpine.data('fileInput', fileInput);
Alpine.data('shareLinks', shareLinks);
Alpine.data('tableOfContents', tableOfContents);
Alpine.data('motionToggle', motionToggle);
Alpine.data('whatsappChat', whatsappChat);

window.Alpine = Alpine;
Alpine.start();

initMotion();
