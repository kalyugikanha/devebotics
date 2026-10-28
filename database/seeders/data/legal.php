<?php

/*
 * Legal page TEMPLATES. They are a starting point only and must be reviewed
 * and completed by qualified legal counsel for your jurisdiction before
 * launch. Edit them in Admin › Website content › Pages.
 */

$notice = '<blockquote><p><strong>Template notice:</strong> this document is a starting template provided with the website. Softec Ai Solution must review and adapt it with legal counsel before relying on it.</p></blockquote>';

return [
    'privacy-policy' => ['Privacy Policy', $notice.<<<'HTML'
<p>This Privacy Policy explains how Softec Ai Solution ("we", "us") collects, uses and protects personal data when you visit our website or contact us.</p>
<h2>Data we collect</h2>
<ul>
<li><strong>Information you provide:</strong> name, email address, phone number, company, project details and any files you attach when you use our contact, project inquiry, feedback, newsletter or job application forms.</li>
<li><strong>Technical data:</strong> IP address (stored in hashed form for abuse prevention), browser type and pages visited, collected through essential cookies and, only with your consent, analytics cookies.</li>
</ul>
<h2>How we use your data</h2>
<ul>
<li>To respond to inquiries and provide requested information or proposals.</li>
<li>To process job applications.</li>
<li>To publish testimonials — only when you explicitly give permission, and showing your name, company or photo only if you separately consent to that.</li>
<li>To send newsletters, only after you confirm your subscription.</li>
<li>To keep the website secure and prevent spam and abuse.</li>
</ul>
<h2>Legal bases</h2>
<p>We process personal data on the basis of your consent, to take steps at your request before entering into a contract, and for our legitimate interests in operating and securing our website. [Confirm the applicable legal bases for your jurisdiction.]</p>
<h2>Retention</h2>
<p>Inquiries and applications are retained only as long as necessary for the purposes above and are deleted or anonymised according to our retention schedule. [Insert retention periods.]</p>
<h2>Sharing</h2>
<p>We do not sell personal data. We share it only with service providers who help us operate the website (such as hosting and email delivery) under appropriate agreements, or where required by law.</p>
<h2>Your rights</h2>
<p>Depending on where you live, you may have the right to access, correct, delete or restrict the use of your personal data, to object to processing and to withdraw consent at any time. Contact us using the details on our Contact page to exercise these rights.</p>
<h2>Cookies</h2>
<p>See our Cookie Policy for details. You can change your cookie preferences at any time using the "Cookie preferences" link in the website footer.</p>
<h2>Contact</h2>
<p>For privacy questions, please use the details on our Contact page. [Insert data protection contact.]</p>
HTML],
    'terms-and-conditions' => ['Terms & Conditions', $notice.<<<'HTML'
<p>These terms govern your use of the Softec Ai Solution website. By using the website you agree to these terms.</p>
<h2>Use of the website</h2>
<p>You may browse the website and contact us for lawful purposes. You must not attempt to disrupt the website, gain unauthorised access to any system, or submit unlawful, misleading or harmful content.</p>
<h2>Information on the website</h2>
<p>Content is provided for general information. Product features, availability and pricing are confirmed only in a written proposal or agreement.</p>
<h2>Intellectual property</h2>
<p>The website design, text, graphics and logos are owned by or licensed to Softec Ai Solution unless stated otherwise. Third-party trademarks belong to their respective owners.</p>
<h2>Submissions</h2>
<p>When you submit feedback, you confirm it reflects your genuine experience. Testimonials are published only with your permission and after moderation.</p>
<h2>Liability</h2>
<p>[Insert limitation of liability appropriate for your jurisdiction.]</p>
<h2>Changes</h2>
<p>We may update these terms from time to time. The latest version will always be available on this page.</p>
<h2>Governing law</h2>
<p>[Insert governing law and jurisdiction.]</p>
HTML],
    'cookie-policy' => ['Cookie Policy', $notice.<<<'HTML'
<p>This policy explains how Softec Ai Solution uses cookies and similar technologies.</p>
<h2>Essential cookies</h2>
<p>Required for the website to work — for example to keep forms secure (CSRF protection), remember your cookie choices and maintain administrator sessions. These cannot be switched off.</p>
<h2>Preference storage</h2>
<p>We store your light/dark theme choice and dismissed announcements in your browser's local storage. This information never leaves your device.</p>
<h2>Analytics cookies (optional)</h2>
<p>If enabled by us and accepted by you, analytics tools such as Google Analytics help us understand how the website is used. They are loaded only after you give consent.</p>
<h2>Managing your choices</h2>
<p>Use the "Cookie preferences" link in the footer to change your choices at any time. You can also delete cookies through your browser settings.</p>
HTML],
    'accessibility-statement' => ['Accessibility Statement', <<<'HTML'
<p>Softec Ai Solution is committed to making this website accessible to everyone, including people who use assistive technologies.</p>
<h2>Our approach</h2>
<ul>
<li>We aim to conform to the Web Content Accessibility Guidelines (WCAG) 2.2 at level AA.</li>
<li>The website supports keyboard navigation, visible focus indicators, screen readers and reduced-motion preferences.</li>
<li>Colour themes are checked for sufficient contrast in both light and dark modes.</li>
</ul>
<h2>Known limitations</h2>
<p>Some third-party content, such as embedded videos or maps, may not be fully accessible. We continue to test and improve the website.</p>
<h2>Feedback</h2>
<p>If you experience any difficulty using this website, please contact us through the Contact page and tell us about the problem. We will do our best to help and to fix the issue.</p>
HTML],
];
