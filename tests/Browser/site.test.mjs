/**
 * Browser tests for key public journeys (Playwright + axe-core).
 *
 *   php artisan serve            # app running with seeded data
 *   npm run test:browser         # BROWSER_BASE_URL defaults to http://127.0.0.1:8000
 *
 * Uses the locally installed Google Chrome (channel "chrome").
 */
import { test, before, after } from 'node:test';
import assert from 'node:assert/strict';
import { chromium } from 'playwright';
import { AxeBuilder } from '@axe-core/playwright';

const BASE = process.env.BROWSER_BASE_URL ?? 'http://127.0.0.1:8000';
let browser;

before(async () => {
    browser = await chromium.launch({ channel: process.env.BROWSER_CHANNEL ?? 'chrome' });
});

after(async () => {
    await browser?.close();
});

async function open(path, options = {}) {
    const context = await browser.newContext({ viewport: { width: 1280, height: 900 }, ...options });
    const page = await context.newPage();
    const errors = [];
    page.on('pageerror', (error) => errors.push(error.message));
    page.on('console', (message) => message.type() === 'error' && errors.push(message.text()));
    await page.goto(BASE + path, { waitUntil: 'networkidle' });

    return { context, page, errors };
}

for (const path of ['/', '/services/custom-software-development', '/contact', '/blog/how-to-identify-high-value-ai-use-cases-in-your-business', '/feedback']) {
    test(`no serious accessibility violations or console errors on ${path}`, async () => {
        const { context, page, errors } = await open(path);
        await page.evaluate(() => document.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('is-revealed')));
        await page.waitForTimeout(900);

        const results = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa']).analyze();
        const serious = results.violations.filter((v) => ['serious', 'critical'].includes(v.impact));

        assert.deepEqual(serious.map((v) => `${v.id}: ${v.nodes.length} node(s) — ${v.nodes[0]?.target}`), []);
        assert.deepEqual(errors, []);
        await context.close();
    });
}

test('dark mode has no serious contrast violations', async () => {
    const { context, page } = await open('/', { colorScheme: 'dark' });
    await page.evaluate(() => document.querySelectorAll('[data-reveal]').forEach((el) => el.classList.add('is-revealed')));
    await page.waitForTimeout(900);
    assert.equal(await page.evaluate(() => document.documentElement.classList.contains('dark')), true);

    const results = await new AxeBuilder({ page }).withRules(['color-contrast']).analyze();
    assert.deepEqual(results.violations.flatMap((v) => v.nodes.map((n) => n.target.join(' '))), []);
    await context.close();
});

test('mobile layout has no horizontal overflow and the menu traps focus', async () => {
    const { context, page } = await open('/', { viewport: { width: 375, height: 812 }, isMobile: true, hasTouch: true });

    const overflow = await page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
    assert.ok(overflow <= 0, `page overflows horizontally by ${overflow}px`);

    await page.getByRole('button', { name: 'Open menu' }).click();
    const dialog = page.getByRole('dialog', { name: 'Site menu' });
    await dialog.waitFor({ state: 'visible' });
    await page.keyboard.press('Tab');
    assert.equal(await page.evaluate(() => !!document.activeElement?.closest('#mobile-menu')), true);

    await page.keyboard.press('Escape');
    await dialog.waitFor({ state: 'hidden' });
    await context.close();
});

test('theme choice persists across reloads without a flash', async () => {
    const { context, page } = await open('/', { colorScheme: 'light' });
    assert.equal(await page.evaluate(() => document.documentElement.classList.contains('dark')), false);

    await page.evaluate(() => localStorage.setItem('softec-theme', 'dark'));
    await page.reload({ waitUntil: 'domcontentloaded' });
    // The inline head script must have applied the class before the bundle loads.
    assert.equal(await page.evaluate(() => document.documentElement.classList.contains('dark')), true);
    await context.close();
});

test('reduced motion disables reveal animations', async () => {
    const { context, page } = await open('/', { reducedMotion: 'reduce' });
    assert.equal(await page.evaluate(() => document.documentElement.classList.contains('motion-reduced')), true);
    const hidden = await page.evaluate(() => [...document.querySelectorAll('[data-reveal]')].filter((el) => getComputedStyle(el).opacity === '0').length);
    assert.equal(hidden, 0);
    await context.close();
});

test('skip link is the first focusable element', async () => {
    const { context, page } = await open('/contact');
    await page.keyboard.press('Tab');
    assert.equal(await page.evaluate(() => document.activeElement?.textContent?.trim()), 'Skip to main content');
    await context.close();
});

test('cookie consent banner records the choice', async () => {
    const { context, page } = await open('/');
    await page.getByRole('button', { name: 'Essential only' }).click();
    await page.getByRole('dialog', { name: 'Your privacy choices' }).waitFor({ state: 'hidden' });

    const cookies = await context.cookies();
    assert.ok(cookies.some((cookie) => cookie.name === 'softec_consent'));
    await context.close();
});

test('services mega menu opens below the navigation, switches categories and closes with Escape', async () => {
    const { context, page, errors } = await open('/');
    await page.getByRole('button', { name: 'Essential only' }).click();

    const nav = page.getByRole('navigation', { name: 'Primary' });
    const trigger = nav.getByRole('button', { name: 'Services' });
    await trigger.hover();
    const panel = page.locator('#menu-0');
    await panel.waitFor();
    assert.equal(await trigger.getAttribute('aria-expanded'), 'true');

    // Regression: the panel must never overlap the nav labels (it used to, under the announcement bar).
    const triggerBox = await trigger.boundingBox();
    const panelBox = await panel.locator('.mega-panel').boundingBox();
    assert.ok(panelBox.y >= triggerBox.y + triggerBox.height, `panel top ${panelBox.y} overlaps nav bottom ${triggerBox.y + triggerBox.height}`);

    const categories = panel.locator('.mega-category');
    await categories.nth(1).hover();
    assert.equal(await categories.nth(1).getAttribute('aria-pressed'), 'true');
    assert.ok(await panel.getByRole('link', { name: /Machine Learning/ }).first().isVisible());

    await page.keyboard.press('Escape');
    await panel.waitFor({ state: 'hidden' });
    assert.deepEqual(errors, []);
    await context.close();
});

test('whatsapp chat opens, offers quick messages and closes with Escape', async () => {
    const { context, page } = await open('/');
    await page.getByRole('button', { name: 'Essential only' }).click();

    const toggle = page.getByRole('button', { name: 'Chat with us on WhatsApp' });
    await toggle.click();
    const panel = page.getByRole('dialog', { name: /Softec/ });
    await panel.waitFor();
    assert.equal(await toggle.getAttribute('aria-expanded'), 'true');
    assert.ok((await panel.getByRole('list', { name: 'Quick messages' }).getByRole('listitem').count()) > 0);

    await page.keyboard.press('Escape');
    await panel.waitFor({ state: 'hidden' });
    assert.equal(await toggle.evaluate((element) => element === document.activeElement), true);
    await context.close();
});

test('contact form submits and shows a reference number', async () => {
    const { context, page } = await open('/contact');
    await page.getByRole('button', { name: 'Essential only' }).click();

    await page.getByLabel('Full name').fill('Browser Test');
    await page.getByLabel('Business email').fill(`browser-${Date.now()}@example.test`);
    await page.getByLabel('What is this about?').selectOption('sales');
    await page.getByRole('textbox', { name: 'Message' }).fill('Automated browser test submission — please ignore this inquiry.');
    await page.locator('input[name="privacy_consent"]').check();
    await page.waitForTimeout(3500); // the anti-bot timing check rejects instant submissions

    await page.getByRole('button', { name: 'Send message' }).click();
    await page.waitForLoadState('networkidle');

    const status = page.getByRole('status').filter({ hasText: 'your message has been received' });
    await status.waitFor();
    assert.match(await status.innerText(), /SAI-\d{4}-[A-Z0-9]{6}/);
    await context.close();
});
