<?php
require_once __DIR__ . '/../support/config.php';

session_name('ENQUIRIES_ADMIN');
session_start();

if (isset($_GET['logout'])) {
  session_unset();
  session_destroy();
  header('Location: login.php');
  exit;
}

if (empty($_SESSION['enq_auth'])) {
  header('Location: login.php');
  exit;
}

$is_admin = ($_SESSION['enq_role'] ?? '') === 'admin';

$valid_statuses = ['new', 'contacted', 'appointment_fixed', 'consultation_completed', 'converted', 'closed'];

$status_labels = [
  'new' => 'New',
  'contacted' => 'Contacted',
  'appointment_fixed' => 'Appointment Fixed',
  'consultation_completed' => 'Consultation Completed',
  'converted' => 'Converted',
  'closed' => 'Closed',
];

$status_colors = [
  'new' => ['bg' => '#fef3cd', 'text' => '#856404'],
  'contacted' => ['bg' => '#d1ecf1', 'text' => '#0c5460'],
  'appointment_fixed' => ['bg' => '#e8d5f5', 'text' => '#5b2a8c'],
  'consultation_completed' => ['bg' => '#d1f7e8', 'text' => '#0d6b4a'],
  'converted' => ['bg' => '#d4edda', 'text' => '#155724'],
  'closed' => ['bg' => '#f8d7da', 'text' => '#721c24'],
];

$outcome_options = [
  'Proposal to be Sent',
  'Documents Awaited',
  'Client Under Consideration',
  'Follow-up Required',
  'Converted to Client',
  'Not Interested',
  'No Response',
  'Other',
];

$enquiries_file = __DIR__ . '/../support/data/enquiries.json';
$enquiries = [];
if (file_exists($enquiries_file)) {
  $data = json_decode(file_get_contents($enquiries_file), true);
  $enquiries = is_array($data) ? array_reverse($data) : [];
}

// Build KPI counts
$kpi = array_fill_keys($valid_statuses, 0);
foreach ($enquiries as $e) {
  $s = $e['status'] ?? 'new';
  if (isset($kpi[$s])) $kpi[$s]++;
}

if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
  $id = (int)($_POST['id'] ?? 0);
  $new_status = $_POST['status'] ?? '';
  if (in_array($new_status, $valid_statuses, true)) {
    $all = json_decode(file_get_contents($enquiries_file), true) ?: [];
    foreach ($all as &$e) {
      if (($e['id'] ?? 0) === $id) {
        $e['status'] = $new_status;

        if ($new_status === 'consultation_completed') {
          $e['appointment_outcome'] = mb_substr(strip_tags(trim($_POST['appointment_outcome'] ?? '')), 0, 100, 'UTF-8');
          if (($e['appointment_outcome'] ?? '') === 'Converted to Client') {
            $e['service_order_no'] = mb_substr(strip_tags(trim($_POST['service_order_no'] ?? '')), 0, 50, 'UTF-8');
            $e['estimated_fees'] = mb_substr(strip_tags(trim($_POST['estimated_fees'] ?? '')), 0, 50, 'UTF-8');
            $e['assigned_to'] = mb_substr(strip_tags(trim($_POST['assigned_to'] ?? '')), 0, 100, 'UTF-8');
            $e['expected_start_date'] = mb_substr(strip_tags(trim($_POST['expected_start_date'] ?? '')), 0, 20, 'UTF-8');
          } else {
            $e['service_order_no'] = null;
            $e['estimated_fees'] = null;
            $e['assigned_to'] = null;
            $e['expected_start_date'] = null;
          }
        } else {
          $e['appointment_outcome'] = null;
          $e['service_order_no'] = null;
          $e['estimated_fees'] = null;
          $e['assigned_to'] = null;
          $e['expected_start_date'] = null;
        }
        break;
      }
    }
    file_put_contents($enquiries_file, json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
  }
  header('Location: enquiries.php');
  exit;
}

if (isset($_GET['export'])) {
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="enquiries.csv"');
  $out = fopen('php://output', 'w');
  fputcsv($out, ['ID', 'Date', 'Name', 'Mobile', 'Email', 'Organisation', 'Service', 'Mode', 'Preferred Date', 'Preferred Time', 'Message', 'Source', 'IP', 'Status', 'Appointment Outcome', 'Service Order No', 'Estimated Fees', 'Assigned To', 'Expected Start Date']);
  foreach ($enquiries as $e) {
    fputcsv($out, [
      $e['id'] ?? '', $e['enquiry_date'] ?? '', $e['name'] ?? '', $e['mobile'] ?? '',
      $e['email'] ?? '', $e['organisation'] ?? '', $e['service'] ?? '', $e['consultation_mode'] ?? '',
      $e['preferred_date'] ?? '', $e['preferred_time'] ?? '', $e['message'] ?? '',
      $e['source_page'] ?? '', $e['ip_address'] ?? '', $e['status'] ?? '',
      $e['appointment_outcome'] ?? '', $e['service_order_no'] ?? '', $e['estimated_fees'] ?? '',
      $e['assigned_to'] ?? '', $e['expected_start_date'] ?? '',
    ]);
  }
  fclose($out);
  exit;
}

$page_title = 'Consultation Workflow';
require_once __DIR__ . '/../includes/header.php';
?>

<main id="main-content">

<section class="section">
  <div class="container">

    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:32px;">
      <div>
        <h1 style="margin:0;font-family:var(--font-display);font-size:28px;font-weight:700;color:var(--navy);">Consultation Workflow</h1>
        <p style="margin:4px 0 0;color:var(--gray-600);font-size:14px;"><?= count($enquiries) ?> total &middot; <span style="text-transform:capitalize;"><?= $is_admin ? 'Admin' : 'BO' ?></span></p>
      </div>
      <div style="display:flex;gap:8px;">
        <a class="btn btn-outline" href="?export=1">Export CSV</a>
        <a class="btn btn-outline" href="?logout=1">Logout</a>
      </div>
    </div>

    <!-- KPI Cards -->
    <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:32px;">
      <?php foreach ($valid_statuses as $s):
        $c = $status_colors[$s];
      ?>
      <div style="background:<?= $c['bg'] ?>;border-radius:12px;padding:16px 12px;text-align:center;">
        <div style="font-size:28px;font-weight:700;color:<?= $c['text'] ?>;line-height:1.1;"><?= $kpi[$s] ?></div>
        <div style="font-size:11px;font-weight:600;color:<?= $c['text'] ?>;margin-top:4px;text-transform:uppercase;letter-spacing:.5px;"><?= $status_labels[$s] ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <?php if (empty($enquiries)): ?>
      <div style="text-align:center;padding:80px 20px;color:var(--gray-400);font-size:16px;">No enquiries yet.</div>
    <?php else: ?>
      <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
          <thead>
            <tr style="background:var(--gray-50);">
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">#</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Date</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Client</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Contact</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Service</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Mode</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Status</th>
              <?php if ($is_admin): ?><th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Actions</th><?php endif; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($enquiries as $e):
              $cur_status = $e['status'] ?? 'new';
              $sc = $status_colors[$cur_status] ?? $status_colors['new'];
              $show_outcome = $cur_status === 'consultation_completed';
              $show_conversion = $show_outcome && ($e['appointment_outcome'] ?? '') === 'Converted to Client';
            ?>
            <tr style="border-bottom:1px solid var(--gray-100);">
              <td style="padding:10px 12px;vertical-align:top;color:var(--gray-400);"><?= $e['id'] ?? '-' ?></td>
              <td style="padding:10px 12px;vertical-align:top;white-space:nowrap;"><?= date('d-m-Y', strtotime($e['enquiry_date'] ?? '')) ?><br><span style="font-size:11px;color:var(--gray-400);"><?= date('h:i A', strtotime($e['enquiry_date'] ?? '')) ?></span></td>
              <td style="padding:10px 12px;vertical-align:top;font-weight:600;"><?= htmlspecialchars($e['name'] ?? '') ?><?= $e['organisation'] ? '<br><span style="font-size:11px;color:var(--gray-400);">' . htmlspecialchars($e['organisation']) . '</span>' : '' ?></td>
              <td style="padding:10px 12px;vertical-align:top;">
                <a href="tel:<?= htmlspecialchars($e['mobile'] ?? '') ?>" style="color:var(--navy);white-space:nowrap;"><?= htmlspecialchars($e['mobile'] ?? '') ?></a><br>
                <a href="mailto:<?= htmlspecialchars($e['email'] ?? '') ?>" style="color:var(--gray-400);font-size:11px;word-break:break-all;"><?= htmlspecialchars($e['email'] ?? '') ?></a>
              </td>
              <td style="padding:10px 12px;vertical-align:top;"><?= htmlspecialchars($e['service'] ?? '') ?></td>
              <td style="padding:10px 12px;vertical-align:top;white-space:nowrap;"><?= htmlspecialchars($e['consultation_mode'] ?? '-') ?><?= $e['preferred_date'] ? '<br><span style="font-size:11px;color:var(--gray-400);">' . htmlspecialchars($e['preferred_date']) . ($e['preferred_time'] ? ' ' . htmlspecialchars($e['preferred_time']) : '') . '</span>' : '' ?></td>
              <td style="padding:10px 12px;vertical-align:top;">
                <span style="display:inline-block;padding:3px 10px;border-radius:999px;font-size:11px;font-weight:600;background:<?= $sc['bg'] ?>;color:<?= $sc['text'] ?>;"><?= $status_labels[$cur_status] ?? $cur_status ?></span>
              </td>
              <?php if ($is_admin): ?>
              <td style="padding:10px 12px;vertical-align:top;">
                <form method="post" style="display:flex;flex-direction:column;gap:6px;">
                  <input type="hidden" name="id" value="<?= $e['id'] ?? 0 ?>">
                  <select name="status" class="status-select" style="font-size:12px;padding:5px 6px;border:1px solid var(--gray-200);border-radius:4px;min-width:130px;">
                    <?php foreach ($valid_statuses as $s): ?>
                    <option value="<?= $s ?>" <?= $cur_status === $s ? 'selected' : '' ?>><?= $status_labels[$s] ?></option>
                    <?php endforeach; ?>
                  </select>

                  <div class="outcome-wrap" style="display:<?= $show_outcome ? 'block' : 'none' ?>;">
                    <select name="appointment_outcome" style="font-size:12px;padding:5px 6px;border:1px solid var(--gray-200);border-radius:4px;width:100%;">
                      <option value="">Select outcome</option>
                      <?php foreach ($outcome_options as $o): ?>
                      <option value="<?= htmlspecialchars($o) ?>" <?= ($e['appointment_outcome'] ?? '') === $o ? 'selected' : '' ?>><?= htmlspecialchars($o) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="conversion-wrap" style="display:<?= $show_conversion ? 'block' : 'none' ?>;">
                    <input type="text" name="service_order_no" placeholder="Service Order No" value="<?= htmlspecialchars($e['service_order_no'] ?? '') ?>" style="font-size:12px;padding:5px 6px;border:1px solid var(--gray-200);border-radius:4px;width:100%;margin-bottom:4px;">
                    <input type="text" name="estimated_fees" placeholder="Estimated Fees" value="<?= htmlspecialchars($e['estimated_fees'] ?? '') ?>" style="font-size:12px;padding:5px 6px;border:1px solid var(--gray-200);border-radius:4px;width:100%;margin-bottom:4px;">
                    <input type="text" name="assigned_to" placeholder="Assigned To" value="<?= htmlspecialchars($e['assigned_to'] ?? '') ?>" style="font-size:12px;padding:5px 6px;border:1px solid var(--gray-200);border-radius:4px;width:100%;margin-bottom:4px;">
                    <input type="date" name="expected_start_date" value="<?= htmlspecialchars($e['expected_start_date'] ?? '') ?>" style="font-size:12px;padding:5px 6px;border:1px solid var(--gray-200);border-radius:4px;width:100%;">
                  </div>

                  <button class="btn btn-sm btn-primary" type="submit" name="update_status" style="min-height:auto;padding:5px 10px;font-size:12px;">Update</button>
                </form>
              </td>
              <?php endif; ?>
            </tr>
            <?php if ($e['message'] ?? ''): ?>
            <tr style="border-bottom:1px solid var(--gray-100);">
              <td colspan="<?= $is_admin ? 8 : 7 ?>" style="padding:0 12px 10px;color:var(--gray-600);font-size:12px;line-height:1.5;white-space:pre-wrap;"><?= htmlspecialchars($e['message'] ?? '') ?></td>
            </tr>
            <?php endif; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>

</main>

<script>
document.querySelectorAll('.status-select').forEach(function(sel) {
  sel.addEventListener('change', function() {
    var row = this.closest('form');
    var outcomeWrap = row.querySelector('.outcome-wrap');
    var conversionWrap = row.querySelector('.conversion-wrap');
    if (this.value === 'consultation_completed') {
      outcomeWrap.style.display = 'block';
      conversionWrap.style.display = row.querySelector('select[name="appointment_outcome"]').value === 'Converted to Client' ? 'block' : 'none';
    } else {
      outcomeWrap.style.display = 'none';
      conversionWrap.style.display = 'none';
    }
  });
  // outcome change toggles conversion fields
  var outcomeSel = sel.closest('form').querySelector('select[name="appointment_outcome"]');
  if (outcomeSel) {
    outcomeSel.addEventListener('change', function() {
      var conversionWrap = this.closest('form').querySelector('.conversion-wrap');
      conversionWrap.style.display = this.value === 'Converted to Client' ? 'block' : 'none';
    });
  }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
