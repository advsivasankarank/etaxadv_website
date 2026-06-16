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
$enquiries_file = __DIR__ . '/../support/data/enquiries.json';
$enquiries = [];
if (file_exists($enquiries_file)) {
  $data = json_decode(file_get_contents($enquiries_file), true);
  $enquiries = is_array($data) ? array_reverse($data) : [];
}

if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
  $id = (int)($_POST['id'] ?? 0);
  $new_status = $_POST['status'] ?? '';
  if (in_array($new_status, ['new', 'contacted', 'converted', 'closed'], true)) {
    $all = json_decode(file_get_contents($enquiries_file), true) ?: [];
    foreach ($all as &$e) {
      if (($e['id'] ?? 0) === $id) {
        $e['status'] = $new_status;
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
    fputcsv($out, ['ID', 'Date', 'Name', 'Mobile', 'Email', 'Organisation', 'Service', 'Mode', 'Preferred Date', 'Preferred Time', 'Message', 'Source', 'IP', 'Status']);
    foreach ($enquiries as $e) {
      fputcsv($out, [
        $e['id'] ?? '', $e['enquiry_date'] ?? '', $e['name'] ?? '', $e['mobile'] ?? '',
        $e['email'] ?? '', $e['organisation'] ?? '', $e['service'] ?? '', $e['consultation_mode'] ?? '',
        $e['preferred_date'] ?? '', $e['preferred_time'] ?? '', $e['message'] ?? '',
        $e['source_page'] ?? '', $e['ip_address'] ?? '', $e['status'] ?? ''
      ]);
    }
    fclose($out);
    exit;
  }

$page_title = 'Enquiries Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<main id="main-content">

<section class="section">
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:32px;">
      <div>
        <h1 style="margin:0;font-family:var(--font-display);font-size:28px;font-weight:700;color:var(--navy);">Enquiries</h1>
        <p style="margin:4px 0 0;color:var(--gray-600);font-size:14px;"><?= count($enquiries) ?> total submission<?= count($enquiries) !== 1 ? 's' : '' ?> &middot; <span style="text-transform:capitalize;"><?= $is_admin ? 'Admin' : 'BO' ?></span></p>
      </div>
      <div style="display:flex;gap:8px;">
        <a class="btn btn-outline" href="?export=1">Export CSV</a>
        <a class="btn btn-outline" href="?logout=1">Logout</a>
      </div>
    </div>

    <?php if (empty($enquiries)): ?>
      <div style="text-align:center;padding:80px 20px;color:var(--gray-400);font-size:16px;">No enquiries yet.</div>
    <?php else: ?>
      <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
          <thead>
            <tr style="background:var(--gray-50);">
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">ID</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Date</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Name</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Mobile</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Email</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Service</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Mode</th>
              <th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Status</th>
              <?php if ($is_admin): ?><th style="padding:10px 12px;text-align:left;font-weight:600;color:var(--charcoal);border-bottom:2px solid var(--gray-100);white-space:nowrap;">Action</th><?php endif; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($enquiries as $e): ?>
            <tr style="border-bottom:1px solid var(--gray-100);">
              <td style="padding:10px 12px;vertical-align:top;"><?= $e['id'] ?? '-' ?></td>
              <td style="padding:10px 12px;vertical-align:top;white-space:nowrap;"><?= date('d-m-Y', strtotime($e['enquiry_date'] ?? '')) ?><br><span style="font-size:11px;color:var(--gray-400);"><?= date('h:i A', strtotime($e['enquiry_date'] ?? '')) ?></span></td>
              <td style="padding:10px 12px;vertical-align:top;font-weight:600;"><?= htmlspecialchars($e['name'] ?? '') ?></td>
              <td style="padding:10px 12px;vertical-align:top;white-space:nowrap;"><a href="tel:<?= htmlspecialchars($e['mobile'] ?? '') ?>" style="color:var(--navy);"><?= htmlspecialchars($e['mobile'] ?? '') ?></a></td>
              <td style="padding:10px 12px;vertical-align:top;word-break:break-all;"><a href="mailto:<?= htmlspecialchars($e['email'] ?? '') ?>" style="color:var(--navy);"><?= htmlspecialchars($e['email'] ?? '') ?></a></td>
              <td style="padding:10px 12px;vertical-align:top;"><?= htmlspecialchars($e['service'] ?? '') ?></td>
              <td style="padding:10px 12px;vertical-align:top;white-space:nowrap;"><?= htmlspecialchars($e['consultation_mode'] ?? '-') ?><?= $e['preferred_date'] ? '<br><span style="font-size:11px;color:var(--gray-400);">' . htmlspecialchars($e['preferred_date']) . ($e['preferred_time'] ? ' ' . htmlspecialchars($e['preferred_time']) : '') . '</span>' : '' ?></td>
              <td style="padding:10px 12px;vertical-align:top;">
                <span style="display:inline-block;padding:2px 10px;border-radius:999px;font-size:11px;font-weight:600;text-transform:capitalize;<?php
                  $s = $e['status'] ?? 'new';
                  if ($s === 'new') echo 'background:#fef3cd;color:#856404;';
                  elseif ($s === 'contacted') echo 'background:#d1ecf1;color:#0c5460;';
                  elseif ($s === 'converted') echo 'background:#d4edda;color:#155724;';
                  else echo 'background:#f8d7da;color:#721c24;';
                ?>"><?= $s ?></span>
              </td>
              <?php if ($is_admin): ?>
              <td style="padding:10px 12px;vertical-align:top;">
                <form method="post" style="display:flex;gap:4px;">
                  <input type="hidden" name="id" value="<?= $e['id'] ?? 0 ?>">
                  <select name="status" style="font-size:12px;padding:4px 6px;border:1px solid var(--gray-200);border-radius:4px;">
                    <option value="new" <?= ($e['status'] ?? '') === 'new' ? 'selected' : '' ?>>New</option>
                    <option value="contacted" <?= ($e['status'] ?? '') === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                    <option value="converted" <?= ($e['status'] ?? '') === 'converted' ? 'selected' : '' ?>>Converted</option>
                    <option value="closed" <?= ($e['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed</option>
                  </select>
                  <button class="btn btn-sm btn-primary" type="submit" name="update_status" style="min-height:auto;padding:4px 10px;font-size:12px;">Update</button>
                </form>
              </td>
              <?php endif; ?>
            </tr>
            <?php if ($e['message'] ?? ''): ?>
            <tr style="border-bottom:1px solid var(--gray-100);">
              <td colspan="<?= $is_admin ? 9 : 8 ?>" style="padding:0 12px 10px;color:var(--gray-600);font-size:12px;line-height:1.5;white-space:pre-wrap;"><?= htmlspecialchars($e['message'] ?? '') ?></td>
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
