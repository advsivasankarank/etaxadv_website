<?php
$page_title = "Frequently Asked Questions | GST, Income Tax, Labour Law, Compliance | E Tax Advisors";
$page_description = "20 frequently asked questions about GST notices, income tax assessments, labour law compliance, company registration, trust advisory and our consultation process. Practical answers for business decision-makers.";
$page_path = '/faq.php';
require_once __DIR__ . '/includes/header.php';
$faqs = [
  'GST' => [
    ['q' => 'What should I do when I receive a GST show cause notice?', 'a' => 'A GST show cause notice (SCN) requires a detailed written response with supporting documents within the specified timeline — typically 30 days. Do not ignore it. Share the notice with us immediately. We review the notice, compile supporting records, draft the response and submit it before the deadline. We also represent you in personal hearings if required. Call +91 98946 26300 for immediate assistance.'],
    ['q' => 'How long does it take to get GST registration?', 'a' => 'GST registration typically takes 7-15 working days from the date of complete document submission, provided the application is error-free. We handle the entire process — document collection, application filing, ARN generation and follow-up with the GST department for approval.'],
    ['q' => 'What is the difference between GSTR-1 and GSTR-3B?', 'a' => 'GSTR-1 is a monthly/quarterly return of outward supplies (sales). GSTR-3B is a monthly summary return that includes both outward and inward supplies along with tax payment. Both must be filed by the 11th and 20th of the following month respectively (for monthly filers). We handle both filings with reconciliation checks.'],
    ['q' => 'What happens if I miss a GST return filing deadline?', 'a' => 'Late filing attracts interest at 18% per annum (9% for taxpayers with turnover below ₹5Cr under the quarterly filing option) plus a late fee of ₹50 per day (₹25 each for CGST and SGST). Continued non-filing can lead to GST registration suspension or cancellation. We provide proactive deadline reminders and can file belated returns with penalty calculations.'],
    ['q' => 'What is an e-way bill and when is it required?', 'a' => 'An e-way bill is an electronic document required for the inter-state or intra-state movement of goods valued above ₹50,000. It must be generated before the goods are dispatched and contains details of the consignor, consignee, transporter and goods. We provide e-way bill generation and compliance support.'],
  ],
  'Income Tax' => [
    ['q' => 'I received a notice under Section 143(2) for scrutiny assessment. What does this mean?', 'a' => 'A notice under Section 143(2) means the Income Tax Department has selected your return for scrutiny. You are required to provide documents, explanations and justifications for the claims made in your return. This is a serious proceeding — incomplete or delayed responses can lead to adverse assessments. We provide end-to-end representation, from document compilation to personal hearing attendance.'],
    ['q' => 'What is the difference between a defective return notice and a scrutiny notice?', 'a' => 'A defective return notice (Section 139(9)) indicates a technical or procedural defect in your filed return — such as a mismatch in schedules or incomplete information. This can usually be rectified by filing a revised return within 15 days. A scrutiny notice (Section 143(2)) is a detailed examination of your return and requires a much more extensive response.'],
    ['q' => 'How long does an income tax assessment take?', 'a' => 'A scrutiny assessment under Section 143(3) typically takes 6-18 months from the date of notice, depending on the complexity of the case, the responsiveness of the taxpayer, and the workload of the assessing officer. We actively manage the timeline and follow up with the department to avoid unnecessary delays.'],
    ['q' => 'What are the consequences of not responding to an income tax notice?', 'a' => 'Non-response to an income tax notice can result in an ex-parte assessment, where the assessing officer makes a determination based on available information — often unfavourable to the taxpayer. This can lead to significant tax demands, penalties and prolonged litigation. Never ignore a notice.'],
    ['q' => 'Can you represent me before the income tax department?', 'a' => 'Yes. K. Sivasankaran, Advocate, is enrolled with the Bar Council of Tamil Nadu &amp; Puducherry and is authorised to represent taxpayers before the Income Tax Department, CIT(A) and Income Tax Appellate Tribunal (ITAT). We have handled 300+ assessment and appeal matters.'],
  ],
  'Labour Law & HR' => [
    ['q' => 'Which labour laws apply to my business?', 'a' => 'The applicable labour laws depend on your industry, number of employees, location and business structure. Generally, most businesses need to comply with ESI Act, PF Act, Shop &amp; Establishment Act, Contract Labour Act, Payment of Wages Act, Minimum Wages Act, and the new Labour Codes. We conduct a labour law applicability assessment to determine exactly what applies to your business.'],
    ['q' => 'What records and registers must a factory maintain under the Factories Act?', 'a' => 'A factory must maintain registers for adult workers, leave with wages, overtime, fine, deduction, health, safety and welfare. Additional registers are required for contract labour, hazardous processes and canteen operations. Non-maintenance of registers is a common violation found during inspections. We help set up and maintain all statutory registers.'],
    ['q' => 'What is the penalty for non-compliance with ESI or PF regulations?', 'a' => 'ESI and PF non-compliance can result in inspection, show cause notices, demands with interest, penalties up to 25% of the amount due, and in serious cases, prosecution. Directors and principal employers can be held personally liable. Regular compliance is the only safe approach.'],
    ['q' => 'Do I need a POSH committee for my organisation?', 'a' => 'Yes, every organisation with 10 or more employees is required to constitute an Internal Complaints Committee (ICC) under the Sexual Harassment of Women at Workplace (Prevention, Prohibition and Redressal) Act, 2013. We assist in ICC formation, training and annual compliance reporting.'],
    ['q' => 'What is the new Labour Code and how does it affect my business?', 'a' => 'The four Labour Codes — Code on Wages, Industrial Relations Code, Social Security Code and Occupational Safety, Health and Working Conditions Code — consolidate 29 central labour laws. While rules are still being notified, they will affect wage definition, compliance thresholds, registration requirements and penalty structures. We track these developments and advise clients on transition requirements.'],
  ],
  'Company & Trust' => [
    ['q' => 'What are the annual compliance requirements for a Private Limited Company?', 'a' => 'A Private Limited Company must file annual returns with the Registrar of Companies (ROC) — Form AOC-4 (financial statements) and Form MGT-7 (annual return) — within specified deadlines. Additionally, board meetings must be held, directors must file DIR-3 KYC, and income tax returns must be filed. Non-compliance attracts penalties.'],
    ['q' => 'How do I register a trust or society?', 'a' => 'A trust is registered through a trust deed executed on non-judicial stamp paper and registered with the Sub-Registrar. A society requires at least 7 members and is registered with the Registrar of Societies. Each has different governance requirements. We handle the entire registration process including deed/society by-law drafting, stamping and registration.'],
    ['q' => 'What is 12A registration and why is it important for my NGO?', 'a' => 'Section 12A registration under the Income Tax Act is required for trusts and NGOs to claim tax exemption on their income. Without 12A registration, the institution\'s income is taxable. It is also a prerequisite for 80G approval (which allows donors to claim tax deduction). 12A registration must be renewed every 5 years under the new 12AB regime.'],
    ['q' => 'What is FCRA and when does my NGO need it?', 'a' => 'FCRA (Foreign Contribution Regulation Act) registration is mandatory for any NGO, trust or society that wishes to receive foreign funding or contributions. Without FCRA registration, receiving foreign funds is illegal and can result in severe penalties. FCRA registration must be renewed every 5 years.'],
    ['q' => 'What are the compliance requirements for a Section 8 company?', 'a' => 'A Section 8 company (non-profit company) must comply with all ROC filing requirements plus maintain its charitable objectives. It must file annual returns, hold board meetings, maintain proper accounts, and ensure its income is applied toward its objectives. It cannot distribute dividends. We provide end-to-end Section 8 compliance management.'],
  ],
];
?>
<main id="main-content">
  <section class="section">
    <div class="container">
      <div class="section-header">
        <p class="section-label">Frequently Asked Questions</p>
        <h1 class="section-title">Practical answers to the compliance, tax and legal questions we hear most often.</h1>
        <p class="section-intro">These questions come from actual client conversations. If you cannot find what you are looking for, <a href="/contact.php#consult" style="color:var(--navy);font-weight:600;">contact our team directly</a>.</p>
      </div>

<?php foreach ($faqs as $category => $questions): ?>
      <div style="margin-bottom:48px;">
        <h2 style="font-family:var(--font-display);font-size:22px;font-weight:700;color:var(--navy);margin:0 0 20px;padding-bottom:12px;border-bottom:2px solid var(--gold);"><?= htmlspecialchars($category) ?></h2>
        <div style="display:grid;gap:12px;">
<?php foreach ($questions as $faq): ?>
          <div class="card" style="padding:24px;">
            <h3 style="font-family:var(--font-display);font-size:16px;font-weight:700;margin:0 0 8px;color:var(--navy);"><?= htmlspecialchars($faq['q']) ?></h3>
            <p style="color:var(--gray-600);font-size:14px;line-height:1.7;margin:0;"><?= htmlspecialchars($faq['a']) ?></p>
          </div>
<?php endforeach; ?>
        </div>
      </div>
<?php endforeach; ?>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
